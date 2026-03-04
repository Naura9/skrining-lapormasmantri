<?php

namespace App\Http\Resources\Warga;

use Illuminate\Http\Resources\Json\JsonResource;

class IdentitasAnggotaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'keluarga_id' => $this->keluarga_id,

            'no_kk' => optional($this->keluarga)->no_kk,

            'nama' => $this->nama,
            'nik' => $this->nik,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,

            'jenis_kelamin' => $this->jenis_kelamin,
            'hubungan_keluarga' => $this->hubungan_keluarga,
            'status_perkawinan' => $this->status_perkawinan,
            'pendidikan_terakhir' => $this->pendidikan_terakhir,
            'pekerjaan' => $this->pekerjaan,

            'kepala_keluarga' => [
                'nama' => optional($this->keluarga->kepalaKeluarga)->nama,
                'nik'  => optional($this->keluarga->kepalaKeluarga)->nik,
            ],

            'created_at' => $this->created_at,
        ];
    }
}