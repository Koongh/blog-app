<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Auth\Events\Verified;


class AuthController extends Controller
{

    public function register(Request $request){
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Optionally wrap in DB transaction
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            // Send verification email
            $user->sendEmailVerificationNotification();

            DB::commit();

            return response()->json([
                'message' => 'User registered successfully. Please check your email to verify your account.',
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Register endpoint failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function login(Request $request){
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email before logging in.',
            ], 403);
        }

        $accessToken = $user->createToken('access-token')->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function verifyEmail(Request $request, $id, $hash){
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->email))) {
            return response()->json(['message' => 'Invalid verification link'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        $user->markEmailAsVerified();

        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function resendVerificationEmail(Request $request){
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent']);
    }

}
