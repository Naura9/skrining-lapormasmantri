<?php

namespace Database\Seeders;

use App\Models\KategoriModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_kategori' => 'Keluarga',
                'target_skrining' => 'kk',
            ],
            [
                'nama_kategori' => 'Ibu Hamil',
                'target_skrining' => 'nik',
            ],
            [
                'nama_kategori' => 'Ibu Bersalin',
                'target_skrining' => 'nik',
            ],
            [
                'nama_kategori' => 'Bayi',
                'target_skrining' => 'nik',
            ],
            [
                'nama_kategori' => 'Usia Prasekolah',
                'target_skrining' => 'nik',
            ],
            [
                'nama_kategori' => 'Usia Remaja',
                'target_skrining' => 'nik',
            ],
            [
                'nama_kategori' => 'Usia Produktif',
                'target_skrining' => 'nik',
            ],
            [
                'nama_kategori' => 'Usia Lansia',
                'target_skrining' => 'nik',
            ],
        ];

        foreach ($data as $item) {
            KategoriModel::firstOrCreate(
                ['nama_kategori' => $item['nama_kategori']],
                $item
            );
        }
    }
}
