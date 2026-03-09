<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectRequestController;
use App\Http\Controllers\ProjectApprovalController;
use App\Http\Controllers\ProjectProgressController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Default variables
    $viewData = ['user' => $user];
    
    // For clients, fetch global active queues to show IT workload/queue position
    if ($user->isClient()) {
        $viewData['globalQueues'] = \App\Models\Queue::with(['assignedTo'])
            ->whereIn('status', ['Pending', 'In Progress'])
            ->orderByRaw("FIELD(status, 'In Progress', 'Pending')") // In Progress first
            ->orderBy('created_at', 'asc') // FIFO by creation time
            ->get();
    }
    
    return view('dashboard', $viewData);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Project Request Routes (Authenticated Users)
Route::middleware(['auth'])->group(function () {
    Route::resource('project-requests', ProjectRequestController::class);
    Route::post('project-requests/{projectRequest}/submit', [ProjectRequestController::class, 'submitForApproval'])
        ->name('project-requests.submit');
    Route::post('project-requests/{projectRequest}/resolve', [ProjectRequestController::class, 'resolveTicket'])
        ->name('project-requests.resolve');
    Route::post('project-requests/{projectRequest}/close', [ProjectRequestController::class, 'closeTicket'])
        ->name('project-requests.close');
    Route::post('project-requests/{projectRequest}/upload-requirements', [ProjectRequestController::class, 'uploadRequirements'])
        ->name('project-requests.upload-requirements');
    Route::get('project-requirements/{requirement}/view', [ProjectRequestController::class, 'viewRequirement'])
        ->name('project-requirements.view');
    Route::get('project-requirements/{requirement}/download', [ProjectRequestController::class, 'downloadRequirement'])
        ->name('project-requirements.download');
    Route::delete('project-requirements/{requirement}', [ProjectRequestController::class, 'deleteRequirement'])
        ->name('project-requirements.delete');
});

// Queue Routes
Route::middleware(['auth'])->group(function () {
    Route::resource('queues', \App\Http\Controllers\QueueController::class)->only(['index']);
});

// Approval Routes (Admin and Super Admin only)
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('approvals')->name('approvals.')->group(function () {
    Route::get('/', [ProjectApprovalController::class, 'index'])->name('index');
    Route::get('/{approval}', [ProjectApprovalController::class, 'show'])->name('show');
    Route::post('/{approval}/approve', [ProjectApprovalController::class, 'approve'])->name('approve');
    Route::post('/{approval}/reject', [ProjectApprovalController::class, 'reject'])->name('reject');
    Route::post('/{approval}/request-revision', [ProjectApprovalController::class, 'requestRevision'])->name('request-revision');
});

// Progress Tracking Routes (Authenticated Users)
Route::middleware(['auth'])->prefix('progress')->name('progress.')->group(function () {
    Route::get('/{queue}', [ProjectProgressController::class, 'show'])->name('show');
    Route::get('/{queue}/timeline', [ProjectProgressController::class, 'timeline'])->name('timeline');
    Route::post('/{queue}/update-stage', [ProjectProgressController::class, 'updateStage'])->name('update-stage');
    Route::post('/{queue}/log-activity', [ProjectProgressController::class, 'logActivity'])->name('log-activity');
});

// Chat Routes (Authenticated Users)
Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/create', [ChatController::class, 'create'])->name('create');
    Route::post('/', [ChatController::class, 'store'])->name('store');
    Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
    Route::post('/{conversation}/send', [ChatController::class, 'sendMessage'])->name('send');
    Route::post('/{conversation}/upload', [ChatController::class, 'uploadFile'])->name('upload');
    Route::post('/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
    Route::post('/{conversation}/close', [ChatController::class, 'close'])->name('close');
    Route::post('/{conversation}/reopen', [ChatController::class, 'reopen'])->name('reopen');
    Route::get('/{conversation}/messages', [ChatController::class, 'getMessages'])->name('messages');
});

// Super Admin Routes
Route::middleware(['auth', 'super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/activity-logs', [SuperAdminController::class, 'activityLogs'])->name('activity-logs');
    Route::get('/settings', [SuperAdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [SuperAdminController::class, 'updateSettings'])->name('settings.update');
    Route::get('/reports', [SuperAdminController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [SuperAdminController::class, 'exportMonthlyCumulativeReport'])->name('reports.export');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/activate', [UserManagementController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [UserManagementController::class, 'deactivate'])->name('users.deactivate');
    Route::post('/users/{user}/suspend', [UserManagementController::class, 'suspend'])->name('users.suspend');
});

// Chat API Routes for Popup Widget
Route::middleware(['auth'])->prefix('api/chat')->name('api.chat.')->group(function () {
    Route::get('/conversations', [App\Http\Controllers\ChatController::class, 'index'])->name('conversations');
    Route::get('/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages'])->name('messages');
    Route::post('/{conversation}/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('send');
});

// Notification Routes (Authenticated)
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/counts', [App\Http\Controllers\NotificationController::class, 'getCounts'])->name('counts');
    Route::get('/list', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('list');
    Route::get('/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read-get');
    Route::post('/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
});

// Report Routes (Export PDF)
Route::middleware(['auth'])->group(function () {
    Route::get('/reports/projects/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.projects.pdf');
});

require __DIR__.'/auth.php';
