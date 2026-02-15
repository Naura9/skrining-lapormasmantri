<?php

namespace App\Http\Resources\Skrining;

use Illuminate\Http\Resources\Json\JsonResource;

class PertanyaanResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'kategori_id'      => $this->section->kategori->id,
            'nama_kategori'    => $this->section->kategori->nama_kategori,
            'target_skrining'  => $this->section->kategori->target_skrining,
            'section_id'       => $this->section_id,
            'judul_section'    => $this->section->judul_section,
            'no_urut'          => $this->no_urut,
            'pertanyaan'       => $this->pertanyaan,
            'jenis_jawaban'    => $this->jenis_jawaban,
            'opsi_jawaban'     => $this->opsi_jawaban,
        ];
    }
}
