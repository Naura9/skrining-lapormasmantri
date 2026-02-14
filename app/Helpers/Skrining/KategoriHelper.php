<?php
namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\KategoriModel;
use Throwable;

class KategoriHelper extends Helper
{
    private $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $kategoris = $this->kategoriModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $kategoris
        ];
    }

    public function getById(string $id): array
    {
        $kategori = $this->kategoriModel->getById($id);
        if (!$kategori) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $kategori
        ];
    }

    public function create(array $payload): array
    {
        try {
            $kategori = $this->kategoriModel->store($payload);

            return [
                'status' => true,
                'data' => $kategori
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
            $this->kategoriModel->edit($payload, $id);

            $kategori = $this->getById($id);
            return [
                'status' => true,
                'data' => $kategori['data']
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
            $this->kategoriModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
