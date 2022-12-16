<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        try {
            // validate request
            $request->validate([
                'email'     => 'required|email',
                'password'  => 'required'
            ]);

            // Find user by Email
            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return ResponseFormatter::error('Unathorized', 401);
            }

            $user = User::where('email', $request->email)->first();
            if (!Hash::check($request->password, $user->password)) {
                throw new Exception("Invalid Password");
            }

            // Generate Token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return response
            return ResponseFormatter::success([
                'access_token'  => $tokenResult,
                'token_type'    => 'Bearer',
                'user'          => $user
            ], 'Login Success');

        } catch (Exception $error) {
            return ResponseFormatter::error('Authentication Failded');
        }
    }

    public function register(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', new Password],
            ]);

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate token
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            // Return response
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register success');
        } catch (Exception $error) {
            // Return error response
            return ResponseFormatter::error($error->getMessage());
        }
    }

    public function logout(Request $request)
    {
        // Revoke Token
        $token = $request->user()->currentAccessToken()->delete();

        // Return Response
        return ResponseFormatter::success($token, 'Logout success');
    }

    public function fetch(Request $request)
    {
        $user = $request->user();

        return ResponseFormatter::success($user, 'Fetch Success');
    }
}
