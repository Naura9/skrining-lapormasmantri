<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use Illuminate\Http\Request;
use App\Helpers\Question\QuestionHelper;
use App\Http\Resources\Question\QuestionResource;

class QuestionController extends Controller
{
    private $question;

    public function __construct()
    {
        $this->question = new QuestionHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'question_type' => $request->question_type ?? '',
        ];

        $questions = $this->question->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => QuestionResource::collection($questions['data']['data']),
            'meta' => [
                'total' => $questions['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $question = $this->question->getById($id);

        if (!$question['status']) {
            return response()->failed(['Data soal tidak ditemukan'], 404);
        }

        return response()->success(new QuestionResource($question['data']));
    }

    public function store(QuestionRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['category_id', 'order_no', 'question_text', 'question_type']);
        $question = $this->question->create($payload);

        if (!$question['status']) {
            return response()->failed($question['error']);
        }

        return response()->success(new QuestionResource($question['data']), 'Soal berhasil ditambahkan');
    }

    public function update(QuestionRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'category_id', 'order_no', 'question_text', 'question_type']);
        $question = $this->question->update($payload, $payload['id']);

        if (!$question['status']) {
            return response()->failed($question['error']);
        }

        return response()->success(new QuestionResource($question['data']), 'Soal berhasil diubah');
    }

    public function destroy($id)
    {
        $question = $this->question->delete($id);

        if (!$question) {
            return response()->failed(['Mohon maaf data soal tidak ditemukan']);
        }

        return response()->success($question, 'Soal berhasil dihapus');
    }
}
