<?php

namespace App\Helpers\User;

use App\Helpers\Helper;
use App\Models\SkriningModel;
use App\Models\User\UserModel;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserHelper extends Helper
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $users = $this->userModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $users
        ];
    }

    public function getById(string $id)
    {
        $user = $this->userModel->getById($id);
        if (!$user) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $user
        ];
    }

    public function create(array $payload): array
    {
        try {
            $payload['password'] = Hash::make($payload['password']);

            $user = $this->userModel->create([
                'username' => $payload['username'],
                'name' => $payload['name'],
                'password' => $payload['password'],
                'role' => $payload['role'],
            ]);

            if ($payload['role'] === 'admin') {
                $user->adminDetail()->create([
                    'nik' => $payload['nik'],
                    'no_telepon' => $payload['no_telepon'],
                    'jenis_kelamin' => $payload['jenis_kelamin'],
                ]);
                $user->load('adminDetail');
            }

            if ($payload['role'] === 'nakes') {
                $user->nakesDetail()->create([
                    'kelurahan_id' => $payload['kelurahan_id'],
                    'nik' => $payload['nik'],
                    'no_telepon' => $payload['no_telepon'],
                    'jenis_kelamin' => $payload['jenis_kelamin'],
                ]);
                $user->load('nakesDetail');
            }

            if ($payload['role'] === 'kader') {
                $user->kaderDetail()->create([
                    'posyandu_id' => $payload['posyandu_id'],
                    'no_telepon' => $payload['no_telepon'],
                    'jenis_kelamin' => $payload['jenis_kelamin'],
                    'status' => $payload['status'],
                ]);
                $user->load('kaderDetail');
            }

            $relations = [];
            if ($payload['role'] === 'admin') $relations[] = 'adminDetail';
            if ($payload['role'] === 'nakes') $relations[] = 'nakesDetail';
            if ($payload['role'] === 'kader') $relations[] = 'kaderDetail';

            $user->load($relations);
            return [
                'status' => true,
                'data' => $user
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function update(array $payload, string $id): array
    {
        try {
            $dataUser = [
                'username' => $payload['username'],
                'name' => $payload['name'],
                'role' => $payload['role'],
            ];

            if (!empty($payload['password'])) {
                $dataUser['password'] = Hash::make($payload['password']);
            }

            $this->userModel->edit($dataUser, $id);

            $user = $this->userModel->find($id);

            if ($payload['role'] === 'admin') {
                $user->adminDetail()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nik' => $payload['nik'],
                        'no_telepon' => $payload['no_telepon'],
                        'jenis_kelamin' => $payload['jenis_kelamin'],
                    ]
                );
            }

            if ($payload['role'] === 'nakes') {
                $user->nakesDetail()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'kelurahan_id' => $payload['kelurahan_id'],
                        'nik' => $payload['nik'],
                        'no_telepon' => $payload['no_telepon'],
                        'jenis_kelamin' => $payload['jenis_kelamin'],
                    ]
                );
            }

            if ($payload['role'] === 'kader') {
                $user->kaderDetail()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'posyandu_id' => $payload['posyandu_id'],
                        'no_telepon' => $payload['no_telepon'],
                        'jenis_kelamin' => $payload['jenis_kelamin'],
                        'status' => $payload['status'] ?? null,
                    ]
                );
            }

            $relations = [];
            if ($payload['role'] === 'admin') $relations[] = 'adminDetail';
            if ($payload['role'] === 'nakes') $relations[] = 'nakesDetail';
            if ($payload['role'] === 'kader') $relations[] = 'kaderDetail';

            $user->load($relations);

            return [
                'status' => true,
                'data' => $user
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): array
    {
        try {
            $user = $this->userModel->find($id);
            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'User tidak ditemukan'
                ];
            }

            $isUsed = SkriningModel::where('user_id', $id)->exists();

            if ($isUsed) {
                return [
                    'status' => false,
                    'message' => 'User tidak bisa dihapus karena sudah digunakan pada data skrining'
                ];
            }

            switch ($user->role) {
                case 'admin':
                    $user->adminDetail()?->delete();
                    break;

                case 'nakes':
                    $user->nakesDetail()?->delete();
                    break;

                case 'kader':
                    $user->kaderDetail()?->delete();
                    break;
            }

            $user->delete();

            return [
                'status' => true,
                'message' => 'User berhasil dihapus'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    public function resetPassword(string $id, string $newPassword): array
    {
        try {
            $user = $this->userModel->find($id);

            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'User tidak ditemukan'
                ];
            }

            $user->password = Hash::make($newPassword);
            $user->save();

            return [
                'status' => true,
                'message' => 'Password berhasil direset'
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}
