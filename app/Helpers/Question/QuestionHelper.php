<?php
namespace App\Helpers\Question;

use App\Helpers\Helper;
use App\Models\QuestionModel;
use Illuminate\Support\Facades\Hash;
use Throwable;

class QuestionHelper extends Helper
{
    private $questionModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $questions = $this->questionModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $questions
        ];
    }

    public function getById(string $id): array
    {
        $question = $this->questionModel->getById($id);
        if (!$question) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $question
        ];
    }

    public function create(array $payload): array
    {
        try {
            $question = $this->questionModel->store($payload);

            return [
                'status' => true,
                'data' => $question
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function update(array $payload, string $id): array
    {
        try {
            $this->questionModel->edit($payload, $id);

            $question = $this->getById($id);
            return [
                'status' => true,
                'data' => $question['data']
            ];
        } catch (Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->questionModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
