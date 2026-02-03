<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nama' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
            'adminDetail' => new AdminResource($this->whenLoaded('adminDetail')),
            'nakesDetail' => new NakesResource($this->whenLoaded('nakesDetail')),
            'kaderDetail' => new KaderResource($this->whenLoaded('kaderDetail')),
        ];
    }
}
