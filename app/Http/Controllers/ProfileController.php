<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Notifications\EmailChanged;
use App\Notifications\PasswordChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    public function update(ProfileRequest $request)
    {
        $data = $request->validated();
        /** @var User $user */
        $user = Auth::user();

        $originalEmail = $user->email;
        $originalPassword = $user->password;

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? $user->password, // already hashed by Laravel
        ]);

        // sends notification to the user that their email has been changed and they need to verify it again. This is a security measure to prevent unauthorized email changes.
        if ($originalEmail !== $user->email) {
            // i cant use the notify method because the user has changed their email and the notification will be sent to the new email address. Instead, we will use the sendEmailVerificationNotification method which will send the notification to the original email address.
            Notification::route('mail', $originalEmail)->notify(new EmailChanged($originalEmail, $user));
        }

        if ($originalPassword !== $user->password) {
            Notification::route('mail', $user->email)->notify(new PasswordChanged($user));
            // if the password has been changed, we will log the user out of all other sessions. This is a security measure to prevent unauthorized access to the account.
            Auth::logoutOtherDevices($data['password']);
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
