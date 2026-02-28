<?php

namespace App\Http\Resources\Skrining;

use Illuminate\Http\Resources\Json\JsonResource;

class SkriningResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'keluarga_id' => $this->keluarga_id,
            'user_id' => $this->user_id,
            'tanggal_skrining' => $this->tanggal_skrining,

            'jawaban' => $this->jawaban->map(function ($item) {
                return [
                    'id' => $item->id,
                    'pertanyaan_id' => $item->pertanyaan_id,
                    'anggota_keluarga_id' => $item->anggota_keluarga_id,
                    'value_jawaban' => $item->value_jawaban,
                ];
            }),
        ];
    }
}
