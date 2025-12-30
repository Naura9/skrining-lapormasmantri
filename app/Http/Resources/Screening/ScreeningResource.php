<?php

namespace App\Http\Resources\Screening;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScreeningResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'family_id' => $this->family_id,
            'user_id' => $this->user_id,
            'user_name' => $this->user->name,
            'screening_date' => $this->screening_date,
        ];
    }
}
