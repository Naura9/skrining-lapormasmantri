<?php

namespace App\Http\Resources\Answer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnswerCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function($item) {
            return [
                'id' => $item->id,
                'question_id' => $item->question_id,
                'family_id' => $item->family_id,
                'family_member_id' => $item->family_member_id,
                'screening_id' => $item->screening_id,
                'question_text' => $item->question->question_text ?? null,
                'answer_value' => $item->answer_value,
            ];
        });
    }
}
