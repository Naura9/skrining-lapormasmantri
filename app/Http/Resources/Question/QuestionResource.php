<?php

namespace App\Http\Resources\Question;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category->category_name,
            'order_no' => $this->order_no,
            'question_text' => $this->question_text,
            'question_type' => $this->question_type,
        ];
    }
}
