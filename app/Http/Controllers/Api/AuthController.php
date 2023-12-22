<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
}
