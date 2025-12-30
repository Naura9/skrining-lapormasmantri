<?php
namespace App\Helpers\User;

use App\Helpers\Helper;
use App\Models\UserModel;
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

            $user = $this->userModel->store($payload);

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
            if (isset($payload['password']) && !empty($payload['password'])) {
                $payload['password'] = Hash::make($payload['password']) ?: '';
            } else {
                unset($payload['password']);
            }

            $this->userModel->edit($payload, $id);

            $user = $this->getById($id);
            return [
                'status' => true,
                'data' => $user['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->userModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
