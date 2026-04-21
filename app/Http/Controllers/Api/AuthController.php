<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\UserRequest;
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

    public function updateProfile(ProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        try {
            $user->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'password' => !empty($validated['password'])
                    ? bcrypt($validated['password'])
                    : $user->password,
            ]);


            if ($user->role === 'kader') {

                $user->kaderDetail()->updateOrCreate(
                    ['user_id' => $user->id],
                    array_filter([
                        'no_telepon' => $validated['no_telepon'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    ])
                );
            }

            if ($user->role === 'nakes') {

                $user->nakesDetail()->updateOrCreate(
                    ['user_id' => $user->id],
                    array_filter([
                        'nik' => $validated['nik'] ?? null,
                        'no_telepon' => $validated['no_telepon'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    ])
                );
            }

            if ($user->role === 'admin') {

                $user->adminDetail()->updateOrCreate(
                    ['user_id' => $user->id],
                    array_filter([
                        'nik' => $validated['nik'] ?? null,
                        'no_telepon' => $validated['no_telepon'] ?? null,
                        'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                    ])
                );
            }

            return response()->json([
                'status' => true,
                'message' => 'Profil berhasil diperbarui',
                'data' => $user->load(['kaderDetail', 'nakesDetail', 'adminDetail'])
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
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
