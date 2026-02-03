<?php

namespace App\Http\Resources\Kelurahan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KelurahanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_kelurahan' => $this->nama_kelurahan,
            'jumlah_posyandu' => $this->posyandu->count(),
            'posyandu' => PosyanduResource::collection($this->posyandu),
        ];
    }
}
