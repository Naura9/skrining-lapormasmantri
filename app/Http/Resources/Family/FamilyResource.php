<?php

namespace App\Http\Resources\Family;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'family_card_number' => $this->family_card_number,
            'head_of_family' => $this->head_of_family,
            'address' => $this->address,
            'neighborhood_rt' => $this->neighborhood_rt,
            'neighborhood_rw' => $this->neighborhood_rw,
            'urban_village' => $this->urban_village,
            'posyandu' => $this->posyandu,
        ];
    }
}
