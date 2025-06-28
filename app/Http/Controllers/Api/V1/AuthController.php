<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => ['Invalid email or password.']
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully.'], 204);
    }

    public function refresh(Request $request)
    {
        $request->user()->tokens()->delete(); // Remove all tokens
        $newToken = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'Bearer'
        ], 200);
    }

    public function user(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        Password::sendResetLink($request->only('email'));

        return response()->json([
            'message' => 'Password reset link sent to your email.'
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',      // At least one uppercase
                'regex:/[0-9]/',      // At least one number
                'regex:/[@$!%*#?&]/', // At least one special character
            ],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset successful.'], 200)
            : response()->json(['message' => 'Password reset failed.'], 400);
    }
}

