<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Notifications\EmailChanged;
use App\Notifications\PasswordChanged;
use Illuminate\Http\Request;
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

        // store image if it exists
        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $path = $image->store('profile_images', 'public');
            $user->image_path = $path;
        }

        // update user
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'] ?? $user->password, // already hashed by Laravel
            'image_path' => $user->image_path,
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

    // destroy
    public function destroy()
    {
        /** @var User $user */
        $user = Auth::user();

        // delete the user
        $user->delete();

        // log the user out
        Auth::logout();

        return redirect()->route('login')->with('success', 'Your account has been deleted successfully.');
    }

    // update profile image
    public function updateImage(Request $request)
    {
        // validate image path
        $request->validate([
            'image_path' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048', // 2MB
            ],
        ]);

        /** @var User $user */
        $user = Auth::user();

        // store the image
        if ($request->hasFile('image_path')) {
            $image = $request->file('image_path');
            $path = $image->store('profile_images', 'public');
            $user->image_path = $path;
            // we use save here instead of update because we are only updating the image_path and not the other fields. This is a security measure to prevent mass assignment vulnerabilities.
            $user->save();
        }

        return redirect()->route('profile.edit')->with('success', 'Profile image updated successfully.');
    }

    // delete profile image
    public function deleteImage()
    {
        /** @var User $user */
        $user = Auth::user();

        // delete the profile image
        $user->image_path = null;
        // we use save here instead of update because we are only updating the image_path and not the other fields. This is a security measure to prevent mass assignment vulnerabilities.
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile image deleted successfully.');
    }
}
