<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        unset($validated['avatar']);

        $user->fill($validated);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            if ($file instanceof UploadedFile && $file->isValid()) {
                // Delete old avatar if exists
                if ($user->avatar && ! preg_match('/^[A-Za-z]:\\\\/', $user->avatar) && ! Str::startsWith($user->avatar, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg';
                $fileName = (string) Str::uuid() . '.' . strtolower($extension);
                $path = null;

                try {
                    Storage::disk('public')->makeDirectory('avatars');
                    $path = Storage::disk('public')->putFileAs('avatars', $file, $fileName);
                } catch (\Throwable $exception) {
                    Log::warning('Avatar upload via storage disk failed, trying direct move', [
                        'user_id' => $user->id,
                        'message' => $exception->getMessage(),
                    ]);

                    try {
                        $targetDirectory = storage_path('app/public/avatars');

                        if (! File::exists($targetDirectory)) {
                            File::makeDirectory($targetDirectory, 0755, true);
                        }

                        $file->move($targetDirectory, $fileName);
                        $path = 'avatars/' . $fileName;
                    } catch (\Throwable $fallbackException) {
                        Log::error('Avatar upload failed', [
                            'user_id' => $user->id,
                            'message' => $fallbackException->getMessage(),
                        ]);

                        return Redirect::route('profile.edit')
                            ->withErrors(['avatar' => 'Upload foto profil gagal. Silakan coba lagi.'])
                            ->withInput();
                    }
                }

                if ($path) {
                    $user->avatar = $path;
                }
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
