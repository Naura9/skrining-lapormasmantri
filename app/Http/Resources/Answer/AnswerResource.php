<?php

namespace App\Http\Resources\Answer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'family_id' => $this->family_id,
            'family_member_id' => $this->family_member_id,
            'screening_id' => $this->screening_id,
            'question_text' => $this->question->question_text,
            'answer_value' => $this->answer_value,
        ];
    }
}
