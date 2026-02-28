<?php

namespace App\Http\Resources\Warga;

use Illuminate\Http\Resources\Json\JsonResource;

class KeluargaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unit_rumah_id' => $this->unit_rumah_id,
            'no_kk' => $this->no_kk,

            'nik_kepala_keluarga' => optional($this->kepalaKeluarga)->nik,
            'nama_kepala_keluarga' => optional($this->kepalaKeluarga)->nama,

            'no_telepon' => $this->no_telepon,
        ];
    }
}
