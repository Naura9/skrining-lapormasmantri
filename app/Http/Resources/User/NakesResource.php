<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NakesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kelurahan_id' => $this->kelurahan_id,
            'nama_kelurahan' => $this->kelurahan->nama_kelurahan,
            'nik' => $this->nik,
            'no_telepon' => $this->no_telepon,
            'jenis_kelamin' => $this->jenis_kelamin,
        ];
    }
}
