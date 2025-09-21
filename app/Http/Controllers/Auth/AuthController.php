<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    function __construct() {}
    public function register(RegisterRequest $request)
    {
        try {

            $data = $request->validated();

            $user = User::create([
                'name'      => $data['name'],
                'email'     => $data['email'],
                'user_type' => $data['user_type'] ?? 'customer',
                'password'  => Hash::make($data['password']),
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            return ResponseHelper::success([
                'user'  => $user,
                'token' => $token,
            ], 'User registered successfully', 201);
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }


    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();

            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return ResponseHelper::error('Invalid credentials', 401);
            }

            $token = $user->createToken('api-token')->plainTextToken;

            return ResponseHelper::success([
                'user'  => $user,
                'token' => $token,
            ], 'Login successful');
        } catch (Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }
}
