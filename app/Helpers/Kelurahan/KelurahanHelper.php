<?php

namespace App\Helpers\Kelurahan;

use App\Helpers\Helper;
use App\Models\KelurahanModel;
use App\Models\PosyanduModel;
use App\Models\UnitModel;
use App\Models\User\UserModel;
use Throwable;

class KelurahanHelper extends Helper
{
    private $kelurahanModel;
    private $posyanduModel;

    public function __construct()
    {
        $this->kelurahanModel = new KelurahanModel();
        $this->posyanduModel = new PosyanduModel();
    }

    public function getAll(array $filter, int $itemPerPage = 0, string $sort = ''): array
    {
        $kelurahans = $this->kelurahanModel->getAll($filter, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $kelurahans,
            'links' => array_values($kelurahans->getUrlRange(1, $kelurahans->lastPage())),
            'total' => $kelurahans->total()
        ];
    }


    public function getById(string $id): array
    {
        $kelurahan = $this->kelurahanModel->getById($id);
        if (!$kelurahan) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $kelurahan
        ];
    }

    public function create(array $payload): array
    {
        try {
            $this->beginTransaction();

            $kelurahan = $this->kelurahanModel->store($payload);

            $this->insertUpdateDetail($payload['posyandu'] ?? [], $kelurahan->id);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $kelurahan
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function update(array $payload): array
    {
        try {
            $this->beginTransaction();

            $this->kelurahanModel->edit([
                'nama_kelurahan' => $payload['nama_kelurahan']
            ], $payload['id']);

            $this->deleteDetail($payload['posyandu_deleted'] ?? []);

            $this->insertUpdateDetail($payload['posyandu'] ?? [], $payload['id']);

            $kelurahan = $this->kelurahanModel
                ->with('posyandu')
                ->find($payload['id']);

            $this->commitTransaction();

            return [
                'status' => true,
                'data' => $kelurahan
            ];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function delete(string $kelurahanId)
    {
        try {
            $isUsed =
                UnitModel::where('kelurahan_id', $kelurahanId)->exists() ||
                UserModel::whereHas('nakesDetail', function ($q) use ($kelurahanId) {
                    $q->where('kelurahan_id', $kelurahanId);
                })->exists();

            if ($isUsed) {
                return [
                    'status' => false,
                    'message' => 'Kelurahan tidak bisa dihapus karena masih digunakan pada data lain'
                ];
            }

            $this->beginTransaction();

            $this->posyanduModel->dropByKelurahanId($kelurahanId);

            $this->kelurahanModel->drop($kelurahanId);

            $this->commitTransaction();

            return [
    'status' => true,
    'data' => $kelurahanId,
    'message' => 'Kelurahan berhasil dihapus'
];
        } catch (Throwable $th) {
            $this->rollbackTransaction();

            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }
    
    private function insertUpdateDetail(array $posyandu, string $kelurahanId)
    {
        if (empty($posyandu)) {
            return;
        }

        foreach ($posyandu as $val) {

            if (!isset($val['id'])) {
                $this->posyanduModel->store([
                    'kelurahan_id' => $kelurahanId,
                    'nama_posyandu' => $val['nama_posyandu']
                ]);
            }

            if (isset($val['is_updated']) && $val['is_updated']) {
                $this->posyanduModel->edit($val, $val['id']);
            }
        }
    }

    private function deleteDetail(array $posyanduIds)
    {
        if (empty($posyanduIds)) {
            return;
        }

        $this->posyanduModel
            ->whereIn('id', $posyanduIds)
            ->delete();
    }
}
