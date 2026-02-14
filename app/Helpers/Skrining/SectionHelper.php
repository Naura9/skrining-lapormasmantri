<?php
namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\SectionModel;
use Throwable;

class SectionHelper extends Helper
{
    private $sectionModel;

    public function __construct()
    {
        $this->sectionModel = new SectionModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $sections = $this->sectionModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $sections
        ];
    }

    public function getById(string $id): array
    {
        $section = $this->sectionModel->getById($id);
        if (!$section) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $section
        ];
    }

    public function create(array $payload): array
    {
        try {
            $section = $this->sectionModel->store($payload);

            return [
                'status' => true,
                'data' => $section
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
            $this->sectionModel->edit($payload, $id);

            $section = $this->getById($id);
            return [
                'status' => true,
                'data' => $section['data']
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
            $this->sectionModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
