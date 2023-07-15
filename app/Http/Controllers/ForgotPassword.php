<?php

namespace App\Http\Controllers;

use App\Mail\PassResetMail;
use App\Models\CustomUser;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;



class ForgotPassword extends Controller
{

    public function sendResetLink(Request $request)
    {
        // Validate user input
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the email exists in the database
        $user = CustomUser::where('email', $validatedData['email'])->first();

        if ($user) {
            // Generate a unique reset token
            $resetToken = Str::random(120);
            // Set the expiry date for the reset token (e.g., 1 hour from now)
        $expiryDate = now()->addHours(24);
            
            // Save the reset token in the user's record
            $user->reset_password_token = $resetToken;
            $user->expiry_time = $expiryDate;
            $user->save();

            // Send reset password email
           // Send reset password email
        $mailSent = Mail::to($user->email)->send(new PassResetMail($resetToken));

        if ($mailSent) {
            return response()->json([
                'message' => 'Reset password link sent to your email.'
            ]);
        } else {
            return response()->json([
                'message' => 'Failed to send reset password email.'
            ], 500);
        }
    }

    // Email does not exist in the database
    return response()->json([
        'message' => 'Email not found.'
    ], 404);

    }


    public function resetPassword(Request $request)
{
    // Validate user input
    $validatedData = $request->validate([
        'password' => 'required|min:6',
    ]);

    $token = $request->token;
    // Find the user with the given reset token
    $user = CustomUser::where('reset_password_token', $token)->first();

    if ($user) {
        // Check if the reset token has expired
        if ($user->expiry_time && now()->gt($user->expiry_time)) {
            return response()->json(['message' => 'Reset token has expired.'], 422);
        }

        // Update the user's password
        $user->password = bcrypt($validatedData['password']);
        $user->reset_password_token = null;
        $user->expiry_time = null; // Reset the expiration time
        $user->save();

        return response()->json([
            'message' => 'Password reset successful.',
            'user' => [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'role_id' => $user->role_id
            ]
    
    ]);
    }

    // Reset token is invalid or expired
    return response()->json(['message' => 'Invalid reset token.'], 404);
}
// ...

public function changePassword(Request $request, $id)
{
    $user = CustomUser::findOrFail($id);

    // Validate user input
    $validatedData = $request->validate([
        'password' => 'required|min:6',
    ]);

    if ($user) {
        // Update the user's password
        $user->password = bcrypt($validatedData['password']);
     
        $user->save();

        return response()->json([
            'message' => 'Password reset successful.',
        ]);
    }

    // User not found
    return response()->json(['message' => 'User not found.'], 404);
}



}