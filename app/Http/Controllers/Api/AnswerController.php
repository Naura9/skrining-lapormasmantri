<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnswerRequest;
use Illuminate\Http\Request;
use App\Helpers\Answer\AnswerHelper;
use App\Http\Resources\Answer\AnswerResource;

class AnswerController extends Controller
{
    private $answer;

    public function __construct()
    {
        $this->answer = new AnswerHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            
        ];

        $answers = $this->answer->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => AnswerResource::collection($answers['data']['data']),
            'meta' => [
                'total' => $answers['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $answer = $this->answer->getById($id);

        if (!$answer['status']) {
            return response()->failed(['Data jawaban tidak ditemukan'], 404);
        }

        return response()->success(new AnswerResource($answer['data']));
    }

    public function store(AnswerRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['question_id', 'family_id', 'family_member_id', 'screening_id', 'answer_value']);
        $answer = $this->answer->create($payload);

        if (!$answer['status']) {
            return response()->failed($answer['error']);
        }

        return response()->success(new AnswerResource($answer['data']), 'Data jawaban berhasil ditambahkan');
    }

    public function update(AnswerRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'question_id', 'family_id', 'family_member_id', 'screening_id', 'answer_value']);
        $answer = $this->answer->update($payload, $payload['id']);

        if (!$answer['status']) {
            return response()->failed($answer['error']);
        }

        return response()->success(new AnswerResource($answer['data']), 'Data jawaban berhasil diubah');
    }

    public function destroy($id)
    {
        $answer = $this->answer->delete($id);

        if (!$answer) {
            return response()->failed(['Mohon maaf data jawaban soal tidak ditemukan']);
        }

        return response()->success($answer, 'Data jawaban berhasil dihapus');
    }
}
