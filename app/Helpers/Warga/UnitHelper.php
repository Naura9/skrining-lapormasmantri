<?php
namespace App\Helpers\Jawaban;

use App\Helpers\Helper;
use App\Models\UnitModel;
use Throwable;

class UnitHelper extends Helper
{
    private $unitModel;

    public function __construct()
    {
        $this->unitModel = new UnitModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $units = $this->unitModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $units
        ];
    }

    public function getById(string $id): array
    {
        $unit = $this->unitModel->getById($id);
        if (!$unit) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $unit
        ];
    }

    public function create(array $payload): array
    {
        try {
            $unit = $this->unitModel->store($payload);

            return [
                'status' => true,
                'data' => $unit
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
            $this->unitModel->edit($payload, $id);

            $unit = $this->getById($id);
            return [
                'status' => true,
                'data' => $unit['data']
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
            $this->unitModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
