<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Resources\User\UserResource;

class AuthController extends Controller
{
    public function login(AuthRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'errors' => $request->validator->errors()
            ], 422);
        }

        $credentials = $request->only('username', 'password');
        $login = AuthHelper::login($credentials['username'], $credentials['password']);

        if (! $login['status']) {
            return response()->json([
                'status' => 'failed',
                'error' => $login['error']
            ], 422);
        }

        return response()->json([
            'status' => 'success',
            'data' => $login['data']
        ], 200);
    }

    public function profile()
    {
        $user = auth()->user()->load([
            'adminDetail',
            'nakesDetail',
            'kaderDetail'
        ]);

        return response()->json([
            'status' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function logout()
    {
        $logout = AuthHelper::logout();

        if (! $logout['status']) {
            return response()->json([
                'status' => false,
                'error' => $logout['error'],
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Logout Success!',
            'data' => [],
        ]);
    }
}
