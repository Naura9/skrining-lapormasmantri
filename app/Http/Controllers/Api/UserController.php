<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Helpers\User\UserHelper;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    private $user;

    public function __construct()
    {
        $this->user = new UserHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'name' => $request->name ?? '',
            'username' => $request->username ?? '',
        ];

        $users = $this->user->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => UserResource::collection($users['data']['data']),
            'meta' => [
                'total' => $users['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $user = $this->user->getById($id);

        if (!$user['status']) {
            return response()->failed(['Data user tidak ditemukan'], 404);
        }

        return response()->success(new UserResource($user['data']));
    }

    public function store(UserRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'username',
            'name',
            'password',
            'role',
            'nik',
            'no_telepon',
            'jenis_kelamin',
            'kelurahan_id',
            'posyandu_id',
            'status'
        ]);

        $user = $this->user->create($payload);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(new UserResource($user['data']), 'User berhasil ditambahkan');
    }

    public function update(UserRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'username',
            'name',
            'password',
            'role',
            'nik',
            'no_telepon',
            'jenis_kelamin',
            'kelurahan_id',
            'posyandu_id',
            'status'
        ]);

        $user = $this->user->update($payload, $payload['id']);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(
            new UserResource($user['data']),
            'User berhasil diubah'
        );
    }

    public function destroy($id)
    {
        $user = $this->user->delete($id);

        if (!$user) {
            return response()->failed(['Mohon maaf data pengguna tidak ditemukan']);
        }

        return response()->success($user, 'User berhasil dihapus');
    }
}
