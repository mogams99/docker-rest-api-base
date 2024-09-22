<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function register(Request $request, User $model): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 401,
                'message' => $validator->errors()
            ]);
        }

        try {
            DB::beginTransaction();
            $overwriteRequest = $request->all();
            $overwriteRequest['password'] = Hash::make($overwriteRequest['password']);
            $createdUser = $model->create($overwriteRequest);
            DB::commit();

            return response()->json([
                'statusCode' => 200,
                'message' => 'User baru berhasil dibuat.',
                'data' => [
                    'access_token' => $createdUser->createToken($createdUser->name)->plainTextToken,
                    'name' => $createdUser->name,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'statusCode' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function login(Request $request, User $model): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'statusCode' => 401,
                'message' => $validator->errors()
            ]);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'statusCode' => 401,
                'message' => 'Kredensial yang anda masukkan tidak valid.'
            ]);
        }

        $loggedUser = Auth::user();
        
        return response()->json([
            'statusCode' => 200,
            'message' => 'Login berhasil.',
            'data' => [
                'access_token' => $loggedUser->createToken($loggedUser->name)->plainTextToken,
                'name' => $loggedUser->name,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'statusCode' => 200,
            'message' => 'Logout berhasil.',
            'data' => null
        ]);
    }
}
