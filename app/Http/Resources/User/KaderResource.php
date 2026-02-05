<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KaderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'posyandu_id' => $this->posyandu_id,
            'nama_kelurahan' => $this->posyandu->kelurahan->nama_kelurahan,
            'nama_posyandu' => $this->posyandu->nama_posyandu,
            'no_telepon' => $this->no_telepon,
            'jenis_kelamin' => $this->jenis_kelamin,
            'status' => $this->status,
        ];
    }
}
