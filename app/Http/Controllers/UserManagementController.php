<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filters
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15);

        return view('super-admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('super-admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:client,developer,admin,super_admin',
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        ActivityLog::logCreate($user, 'Created new user: ' . $user->name);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load([
            'projectRequests',
            'assignedQueues',
            'clientConversations',
            'developerConversations',
            'activityLogs'
        ]);

        return view('super-admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('super-admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:client,developer,admin,super_admin',
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
            'company' => $validated['company'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        ActivityLog::logUpdate($user, 'Updated user: ' . $user->name);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        ActivityLog::logDelete($user, 'Deleted user: ' . $user->name);

        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function activate(User $user)
    {
        $user->activate();

        ActivityLog::log('activate_user', 'Activated user: ' . $user->name, $user);

        return back()->with('success', 'User activated successfully!');
    }

    public function deactivate(User $user)
    {
        // Prevent deactivating own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->deactivate();

        ActivityLog::log('deactivate_user', 'Deactivated user: ' . $user->name, $user);

        return back()->with('success', 'User deactivated successfully!');
    }

    public function suspend(User $user)
    {
        // Prevent suspending own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account!');
        }

        $user->suspend();

        ActivityLog::log('suspend_user', 'Suspended user: ' . $user->name, $user);

        return back()->with('success', 'User suspended successfully!');
    }
}
