<?php

namespace App\Helpers;

use App\Helpers\Helper;
use App\Http\Resources\User\UserResource;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthHelper extends Helper
{
    public static function login($username, $password)
    {
        try {
            $credentials = ['username' => $username, 'password' => $password];
            if (! $token = JWTAuth::attempt($credentials)) {
                return [
                    'status' => false,
                    'error' => ['Kombinasi username dan password yang kamu masukkan salah'],
                ];
            }
        } catch (JWTException $e) {
            return [
                'status' => false,
                'error' => ['Could not create token.'],
            ];
        }

        return [
            'status' => true,
            'data' => self::createNewToken($token),
        ];
    }

    protected static function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => new UserResource(auth()->user()),
        ];
    }

    public static function logout()
    {
        try {
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                return [
                    'status' => true,
                    'message' => 'Logout Success!',
                ];
            }
        } catch (JWTException $e) {

            return [
                'status' => false,
                'error' => ['Could not logout token.'],
            ];
        }
    }
}
