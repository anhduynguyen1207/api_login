<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'message' => ['Username or password are incorrect.Try again!!!'],
            ]);
        }
        $expiresAt = Carbon::now()->addMinutes();
        $user = Auth::user();
        $token = $user->createToken('access-token', ["*"], $expiresAt)->plainTextToken;
        // $refreshToken = $user->createToken('refresh-token')->plainTextToken;
        return response()->json(['message' => 'Logged in successfully', 'token' => $token,'expires' => $expiresAt]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function refreshToken(Request $request)
    {

        if (empty($token = $request->header('Authorization'))) {
            return response()->json(['message' => 'Token is invalid'], 422);
        }

        $token = explode('Bearer ', $token);
        if (empty($token[1]) || empty($token = PersonalAccessToken::findToken($token[1]))) {
            return response()->json(['message' => 'Token is invalid'], 422);
        }

        if (!$token->tokenable instanceof User) {
            return response()->json(['message' => 'Token is invalid'], 422);
        }
        $expiresAt = Carbon::now()->addMinutes();
        return response()->json([
            'status' => 'success',
            'data' => ['access_token' => $token->tokenable->createToken('access-token', ["*"], $expiresAt)->plainTextToken,'expires' => $expiresAt]
        ]);
    }
    public function getAll()
    {
        return response()->json(['message' => 'GetAll list']);
    }
}
