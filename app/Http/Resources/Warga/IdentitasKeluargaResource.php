<?php

namespace App\Http\Resources\Warga;

use Illuminate\Http\Resources\Json\JsonResource;

class IdentitasKeluargaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'kelurahan_id' => $this->kelurahan_id,
            'nama_kelurahan' => $this->kelurahan->nama_kelurahan,
            'posyandu_id'  => $this->posyandu_id,
            'nama_posyandu'  => $this->posyandu->nama_posyandu,
            'alamat' => $this->alamat,
            'rt' => $this->rt,
            'rw' => $this->rw,

            'keluarga' => $this->keluarga->map(function ($item) {
                return [
                    'id' => $item->id,
                    'no_kk' => $item->no_kk,
                    'is_luar_wilayah' => (bool) $item->is_luar_wilayah,
                    'alamat_ktp' => $item->alamat_ktp,
                    'rt_ktp' => $item->rt_ktp,
                    'rw_ktp' => $item->rw_ktp,
                    'no_telepon' => $item->no_telepon,

                    'kepala_keluarga' => [
                        'nama' => optional($item->kepalaKeluarga)->nama,
                        'nik'  => optional($item->kepalaKeluarga)->nik,
                    ]
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
