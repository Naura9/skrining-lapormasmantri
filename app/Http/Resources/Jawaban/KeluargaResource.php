<?php

namespace App\Http\Resources\Jawaban;

use Illuminate\Http\Resources\Json\JsonResource;

class KeluargaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unit_rumah_id' => $this->unit_rumah_id,
            'no_kk' => $this->no_kk,
            'kepala_keluarga' => $this->kepala_keluarga,
        ];
    }
}
