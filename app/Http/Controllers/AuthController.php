<?php

namespace App\Http\Controllers;

// use App\Models\Auth;
// use App\Http\Requests\StoreAuthRequest;
// use App\Http\Requests\UpdateAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;        
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        logger('Token exists?', [
            'exists' => \Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken()) !== null
        ]);
        
        return response()->json(['message' => 'Logged out successfully']);
    }
}
