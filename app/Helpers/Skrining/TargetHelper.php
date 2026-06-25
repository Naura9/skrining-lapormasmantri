<?php

namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\TargetModel;
use Throwable;

class TargetHelper extends Helper
{
    private $targetModel;

    public function __construct()
    {
        $this->targetModel = new TargetModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $targets = $this->targetModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $targets
        ];
    }

    public function getById(string $id): array
    {
        $target = $this->targetModel->getById($id);
        if (!$target) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $target
        ];
    }

    public function create(array $payload): array
    {
        try {
            $target = $this->targetModel->store($payload);

            return [
                'status' => true,
                'data' => $target
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
            $this->targetModel->edit($payload, $id);

            $target = $this->getById($id);
            return [
                'status' => true,
                'data' => $target['data']
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
            $this->targetModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getByKelurahan(string $kelurahanId, ?string $kategoriId = null): array
    {
        $query = $this->targetModel
            ->with(['kelurahan', 'kategori'])
            ->where('kelurahan_id', $kelurahanId);

        if (!empty($kategoriId)) {
            $query->where('kategori_id', $kategoriId);
        }

        $target = $query->first();

        return [
            'status' => (bool) $target,
            'data' => $target
        ];
    }
}
