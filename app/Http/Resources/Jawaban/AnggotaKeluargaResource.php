<?php

namespace App\Http\Resources\Jawaban;

use Illuminate\Http\Resources\Json\JsonResource;

class AnggotaKeluargaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'keluarga_id' => $this->keluarga_id,

            'nama' => $this->nama,
            'nik' => $this->nik,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,

            'no_kk_asal' => $this->no_kk_asal,
            'hubungan_keluarga' => $this->hubungan_keluarga,
            'status_perkawinan' => $this->status_perkawinan,
            'pendidikan_terakhir' => $this->pendidikan_terakhir,
            'pekerjaan' => $this->pekerjaan,
        ];
    }
}
