<?php
namespace App\Helpers\Answer;

use App\Helpers\Helper;
use App\Models\AnswerModel;
use Throwable;

class AnswerHelper extends Helper
{
    private $answerModel;

    public function __construct()
    {
        $this->answerModel = new AnswerModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $answers = $this->answerModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $answers
        ];
    }

    public function getById(string $id): array
    {
        $answer = $this->answerModel->getById($id);
        if (!$answer) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $answer
        ];
    }

    public function create(array $payload): array
    {
        try {
            $answer = $this->answerModel->store($payload);

            return [
                'status' => true,
                'data' => $answer
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
            $this->answerModel->edit($payload, $id);

            $answer = $this->getById($id);
            return [
                'status' => true,
                'data' => $answer['data']
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
            $this->answerModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
