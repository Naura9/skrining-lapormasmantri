<?php

namespace App\Http\Resources\Skrining;

use Illuminate\Http\Resources\Json\JsonResource;

class TargetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'kelurahan_id' => $this->kelurahan_id,
            'nama_kelurahan' => $this->kelurahan->nama_kelurahan,
            'kategori_id' => $this->kategori_id,
            'nama_kategori' => $this->kategori->nama_kategori,
            'target' => $this->target,
        ];
    }
}
