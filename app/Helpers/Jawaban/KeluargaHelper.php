<?php
namespace App\Helpers\Jawaban;

use App\Helpers\Helper;
use App\Models\KeluargaModel;
use Throwable;

class KeluargaHelper extends Helper
{
    private $keluargaModel;

    public function __construct()
    {
        $this->keluargaModel = new KeluargaModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $keluargas = $this->keluargaModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $keluargas
        ];
    }

    public function getById(string $id): array
    {
        $keluarga = $this->keluargaModel->getById($id);
        if (!$keluarga) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $keluarga
        ];
    }

    public function create(array $payload): array
    {
        try {
            $keluarga = $this->keluargaModel->store($payload);

            return [
                'status' => true,
                'data' => $keluarga
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
            $this->keluargaModel->edit($payload, $id);

            $keluarga = $this->getById($id);
            return [
                'status' => true,
                'data' => $keluarga['data']
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
            $this->keluargaModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
