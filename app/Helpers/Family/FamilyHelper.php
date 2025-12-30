<?php
namespace App\Helpers\Family;

use App\Helpers\Helper;
use App\Models\FamilyModel;
use Throwable;

class FamilyHelper extends Helper
{
    private $familyModel;

    public function __construct()
    {
        $this->familyModel = new FamilyModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $families = $this->familyModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $families
        ];
    }

    public function getById(string $id): array
    {
        $family = $this->familyModel->getById($id);
        if (!$family) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $family
        ];
    }

    public function create(array $payload): array
    {
        try {
            $family = $this->familyModel->store($payload);

            return [
                'status' => true,
                'data' => $family
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
            $this->familyModel->edit($payload, $id);

            $family = $this->getById($id);
            return [
                'status' => true,
                'data' => $family['data']
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
            $this->familyModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
