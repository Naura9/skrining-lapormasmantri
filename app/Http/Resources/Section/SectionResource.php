<?php

namespace App\Http\Resources\Section;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'kategori_id'   => $this->kategori_id,
            'nama_kategori' => $this->kategori->nama_kategori,
            'judul_section' => $this->judul_section,
            'no_urut'       => $this->no_urut,
        ];
    }
}
