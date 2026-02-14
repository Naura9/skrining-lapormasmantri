<?php
namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\PertanyaanModel;
use Throwable;

class PertanyaanHelper extends Helper
{
    private $pertanyaanModel;

    public function __construct()
    {
        $this->pertanyaanModel = new PertanyaanModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $pertanyaans = $this->pertanyaanModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $pertanyaans
        ];
    }

    public function getById(string $id): array
    {
        $pertanyaan = $this->pertanyaanModel->getById($id);
        if (!$pertanyaan) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $pertanyaan
        ];
    }

    public function create(array $payload): array
    {
        try {
            $pertanyaan = $this->pertanyaanModel->store($payload);

            return [
                'status' => true,
                'data' => $pertanyaan
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
            $this->pertanyaanModel->edit($payload, $id);

            $pertanyaan = $this->getById($id);
            return [
                'status' => true,
                'data' => $pertanyaan['data']
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
            $this->pertanyaanModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
