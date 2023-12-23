<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // JWT Auth
    public function register(RegisterRequest $request) : \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = User::create([
                'name'     => $request->input('name'),
                'email'    => $request->input('email'),
                'password' => Hash::make($request->input('password')),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 201); // Use 201 for resource creation

        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('User registration failed: ' . $e->getMessage());
            DB::rollback();

            return response()->json(['error' => 'Registration failed', 'validation_errors' => $request->errors()], 422);
        }
    }

    public function login(LoginRequest $request)
    {
        // Get the credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // Attempt to authenticate the user and generate a token
            if (!$token = JWTAuth::attempt($credentials)) {
                // Check if the email exists in the database
                $user = User::where('email', $request->email)->first();

                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }

                // Check if the password matches
                if (!Hash::check(request('password'), $user->getAuthPassword())) {
                    return response()->json(['error' => 'Invalid password'], 401);
                }

                // If neither email nor password match, return a generic error
                return response()->json(['error' => 'Invalid email or password'], 401);
            }

            // Fetch the user details
            $user = Auth::user();

            // Attach the token to the user data in the response
            $user->token = $token;

            return response()->json([
                'status' => 'success',
                'data' => $user
            ], 200);

        } catch (JWTException $e) {
            // Log the exception for debugging purposes
            Log::error('Login failed: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Login failed'], 500);
        }
    }

}
