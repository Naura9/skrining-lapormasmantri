<?php
namespace App\Helpers\Warga;

use App\Helpers\Helper;
use App\Models\AnggotaKeluargaModel;
use Throwable;

class AnggotaKeluargaHelper extends Helper
{
    private $anggotaKeluargaModel;

    public function __construct()
    {
        $this->anggotaKeluargaModel = new AnggotaKeluargaModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $anggotaKeluargas = $this->anggotaKeluargaModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $anggotaKeluargas
        ];
    }

    public function getById(string $id): array
    {
        $anggotaKeluarga = $this->anggotaKeluargaModel->getById($id);
        if (!$anggotaKeluarga) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $anggotaKeluarga
        ];
    }

    public function create(array $payload): array
    {
        try {
            $anggotaKeluarga = $this->anggotaKeluargaModel->store($payload);

            return [
                'status' => true,
                'data' => $anggotaKeluarga
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
            $this->anggotaKeluargaModel->edit($payload, $id);

            $anggotaKeluarga = $this->getById($id);
            return [
                'status' => true,
                'data' => $anggotaKeluarga['data']
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
            $this->anggotaKeluargaModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
