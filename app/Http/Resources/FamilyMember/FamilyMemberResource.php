<?php

namespace App\Http\Resources\FamilyMember;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMemberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                   => $this->id,
            'family_id'            => $this->family_id,
            'head_of_family'       => $this->family->head_of_family,
            'full_name'            => $this->full_name,
            'national_id_number'   => $this->national_id_number,
            'place_of_birth'       => $this->place_of_birth,
            'date_of_birth'        => $this->date_of_birth,
            'gender'               => $this->gender,
            'relationship'         => $this->relationship,
            'marital_status'       => $this->marital_status,
            'last_education'       => $this->last_education,
            'occupation'           => $this->occupation,
        ];
    }
}
