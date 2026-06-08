<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KelurahanModel;
use App\Models\PosyanduModel;

class KelurahanPosyanduSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Bendogerit' => [
                'Bendogerit Patimura',
                'Bendogerit Sudanco Supriyadi',
                'Bendogerit Soekarno',
                'Bendogerit Cut Mutia',
                'Bendogerit Cut Nyak Dien',
                'Bendogerit Moh Hatta',
                'Bendogerit Kartini',
            ],

            'Gedog' => [
                'Gedog Venus',
                'Gedog Jupiter',
                'Gedog Merkurius',
                'Gedog Bumi',
                'Gedog Neptunus',
                'Gedog Saturnus',
                'Gedog Uranus',
                'Gedog Mars',
            ],

            'Karangtengah' => [
                'Karangtengah Wakatobi',
                'Karangtengah Bunaken',
                'Karangtengah Sumba',
                'Karangtengah Nusa Penida Yonif 511',
                'Karangtengah Nusa Penida Anggrek',
                'Karangtengah Mentawai',
            ],

            'Klampok' => [
                'Klampok Sagitarius',
                'Klampok Gemini',
                'Klampok Leo',
                'Klampok Aries',
            ],

            'Plosokerep' => [
                'Plosokerep Ploker Sehat 1',
                'Plosokerep Ploker Sehat 2',
                'Plosokerep Ploker Sehat 3',
                'Plosokerep Ploker Sehat 4',
            ],

            'Rembang' => [
                'Rembang Nakula',
                'Rembang Sadewa',
            ],

            'Sananwetan' => [
                'Sananwetan Srikandi Larasati',
                'Sananwetan Sembodro',
                'Sananwetan Pergiwowati',
                'Sananwetan Dewi Setyowati',
                'Sananwetan Tara Purbowati',
                'Sananwetan Bangkid',
                'Sananwetan Dewi Arimbi',
                'Sananwetan Mustokoweni Rahayu',
                'Sananwetan Pandawa Shinta',
            ],
        ];

        foreach ($data as $namaKelurahan => $posyandus) {

            $kelurahan = KelurahanModel::create([
                'nama_kelurahan' => $namaKelurahan,
            ]);

            foreach ($posyandus as $namaPosyandu) {
                PosyanduModel::create([
                    'kelurahan_id' => $kelurahan->id,
                    'nama_posyandu' => $namaPosyandu,
                ]);
            }
        }
    }
}