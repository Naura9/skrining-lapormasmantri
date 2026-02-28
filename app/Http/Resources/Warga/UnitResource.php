<?php

namespace App\Http\Resources\Warga;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'kelurahan_id' => $this->kelurahan_id,
            'nama_kelurahan' => $this->kelurahan->nama_kelurahan,
            'posyandu_id' => $this->posyandu->id,
            'nama_posyandu' => $this->posyandu->nama_posyandu,
            'alamat' => $this->alamat,
            'rt' => $this->rt,
            'rw' => $this->rw,
        ];
    }
}
