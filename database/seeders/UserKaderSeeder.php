<?php

namespace Database\Seeders;

use App\Models\PosyanduModel;
use App\Models\User\UserKaderModel;
use App\Models\User\UserModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserKaderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posyandu = PosyanduModel::where(
            'nama_posyandu',
            'Bendogerit Patimura'
        )->firstOrFail();

        $user = UserModel::create([
            'name' => 'Kader Bendogerit Patimura',
            'username' => 'kader1',
            'password' => Hash::make('Kader1'),
            'role' => 'kader',
        ]);

        UserKaderModel::create([
            'user_id' => $user->id,
            'posyandu_id' => $posyandu->id,
            'no_telepon' => '081234567890',
            'jenis_kelamin' => 'P',
            'status' => 'aktif',
        ]);
    }
}
