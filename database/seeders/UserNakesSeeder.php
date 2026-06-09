<?php

namespace Database\Seeders;

use App\Models\KelurahanModel;
use App\Models\User\UserModel;
use App\Models\User\UserNakesModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserNakesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelurahan = KelurahanModel::where(
            'nama_kelurahan',
            'Bendogerit'
        )->firstOrFail();

        $user = UserModel::create([
            'name' => 'Nakes Bendogerit',
            'username' => 'nakes1',
            'password' => Hash::make('Nakes1'),
            'role' => 'nakes',
        ]);

        UserNakesModel::create([
            'user_id' => $user->id,
            'kelurahan_id' => $kelurahan->id,
            'nik' => '3572010101010001',
            'no_telepon' => '081234567891',
            'jenis_kelamin' => 'P',
        ]);
    }
}
