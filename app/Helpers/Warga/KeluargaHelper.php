<?php

namespace App\Helpers\Warga;

use App\Helpers\Helper;
use App\Models\AnggotaKeluargaModel;
use App\Models\KeluargaModel;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();

        try {
            $nik = $payload['nik_kepala_keluarga'];
            $nama = $payload['nama_kepala_keluarga'];

            unset($payload['nik_kepala_keluarga'], $payload['nama_kepala_keluarga']);

            $keluarga = $this->keluargaModel->store($payload);

            AnggotaKeluargaModel::create([
                'keluarga_id' => $keluarga->id,
                'nama' => $nama,
                'nik' => $nik,
                'hubungan_keluarga' => 'kepala_keluarga'
            ]);

            DB::commit();

            return [
                'status' => true,
                'data' => $keluarga->fresh('kepalaKeluarga')
            ];
        } catch (Throwable $th) {
            DB::rollBack();

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }

    public function update(array $payload, string $id): array
    {
        DB::beginTransaction();

        try {

            $nik = $payload['nik_kepala_keluarga'];
            $nama = $payload['nama_kepala_keluarga'];

            unset($payload['nik_kepala_keluarga'], $payload['nama_kepala_keluarga']);

            $this->keluargaModel->edit($payload, $id);

            $kepala = AnggotaKeluargaModel::where('keluarga_id', $id)
                ->where('hubungan_keluarga', 'kepala_keluarga')
                ->first();

            if ($kepala) {
                $kepala->update([
                    'nama' => $nama,
                    'nik' => $nik
                ]);
            } else {
                AnggotaKeluargaModel::create([
                    'keluarga_id' => $id,
                    'nama' => $nama,
                    'nik' => $nik,
                    'hubungan_keluarga' => 'Kepala Keluarga'
                ]);
            }

            DB::commit();

            return [
                'status' => true,
                'data' => $this->getById($id)['data']
            ];
        } catch (Throwable $th) {
            DB::rollBack();

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
