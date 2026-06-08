<?php

namespace Database\Seeders;

use App\Models\User\UserAdminModel;
use App\Models\User\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = UserModel::create([
            'name' => 'Admin1',
            'username' => 'admin1',
            'password' => Hash::make('Admin123'),
            'role' => 'admin',
        ]);

        UserAdminModel::create([
            'user_id' => $user->id,
            'nik' => '0000000000000000',
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'L',
        ]);
    }
}