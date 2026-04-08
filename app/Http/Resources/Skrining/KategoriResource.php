<?php

namespace App\Http\Resources\Skrining;

use Illuminate\Http\Resources\Json\JsonResource;

class KategoriResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nama_kategori' => $this->nama_kategori,
            'target_skrining' => $this->target_skrining,
            'created_at' => $this->created_at,
        ];
    }
}
