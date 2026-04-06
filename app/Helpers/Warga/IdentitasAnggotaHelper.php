<?php

namespace App\Helpers\Warga;

use App\Helpers\Helper;
use App\Models\UnitModel;
use App\Models\KeluargaModel;
use App\Models\AnggotaKeluargaModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class IdentitasAnggotaHelper extends Helper
{
    private $anggotaModel;

    public function __construct()
    {
        $this->anggotaModel = new AnggotaKeluargaModel();
    }

    public function getAll(array $filter, int $page = 1, int $perPage = 25)
    {
        $query = AnggotaKeluargaModel::with([
            'keluarga',
            'keluarga.unitRumah.kelurahan',
            'keluarga.unitRumah.posyandu',
            'keluarga.kepalaKeluarga'
        ]);

        if (!empty($filter['keyword'])) {
            $keyword = $filter['keyword'];

            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'LIKE', "%$keyword%")
                    ->orWhere('nik', 'LIKE', "%$keyword%")
                    ->orWhereHas('keluarga', function ($q2) use ($keyword) {
                        $q2->where('no_kk', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('keluarga.unitRumah.kelurahan', function ($q3) use ($keyword) {
                        $q3->where('nama_kelurahan', 'LIKE', "%$keyword%");
                    })
                    ->orWhereHas('keluarga.unitRumah.posyandu', function ($q4) use ($keyword) {
                        $q4->where('nama_posyandu', 'LIKE', "%$keyword%");
                    });
            });
        }

        if (!empty($filter['kelurahan_id'])) {
            $query->whereHas('keluarga.unitRumah.kelurahan', function ($q) use ($filter) {
                $q->where('id', $filter['kelurahan_id']);
            });
        }

        if (!empty($filter['posyandu_id'])) {
            $query->whereHas('keluarga.unitRumah.posyandu', function ($q) use ($filter) {
                $q->where('id', $filter['posyandu_id']);
            });
        }

        $query->orderBy('created_at', 'desc');

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function getById(string $id): array
    {
        $anggota = $this->anggotaModel->getById($id);

        if (!$anggota) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $anggota
        ];
    }

    public function create(array $payload): array
    {
        DB::beginTransaction();

        try {

            $keluarga = KeluargaModel::find($payload['keluarga_id']);

            if (!$keluarga) {
                return [
                    'status' => false,
                    'error' => 'Data keluarga tidak ditemukan'
                ];
            }

            $existing = AnggotaKeluargaModel::where('nik', $payload['nik'])->first();

            if ($existing) {
                $existing->update([
                    'nama'               => $payload['nama'],
                    'tempat_lahir'       => $payload['tempat_lahir'],
                    'tanggal_lahir'      => $payload['tanggal_lahir'],
                    'jenis_kelamin'      => $payload['jenis_kelamin'],
                    'hubungan_keluarga'  => $payload['hubungan_keluarga'],
                    'status_perkawinan'  => $payload['status_perkawinan'],
                    'pendidikan_terakhir' => $payload['pendidikan_terakhir'],
                    'pekerjaan'          => $payload['pekerjaan'],
                ]);

                DB::commit();

                return [
                    'status' => true,
                    'data'   => $existing
                ];
            }

            $anggota = AnggotaKeluargaModel::create([
                'keluarga_id'        => $keluarga->id,
                'nama'               => $payload['nama'],
                'nik'                => $payload['nik'],
                'tempat_lahir'       => $payload['tempat_lahir'],
                'tanggal_lahir'      => $payload['tanggal_lahir'],
                'jenis_kelamin'      => $payload['jenis_kelamin'],
                'hubungan_keluarga'  => $payload['hubungan_keluarga'],
                'status_perkawinan'  => $payload['status_perkawinan'],
                'pendidikan_terakhir' => $payload['pendidikan_terakhir'],
                'pekerjaan'          => $payload['pekerjaan'],
            ]);

            DB::commit();

            return [
                'status' => true,
                'data'   => $anggota
            ];
        } catch (Throwable $th) {

            DB::rollBack();

            return [
                'status' => false,
                'error'  => $th->getMessage()
            ];
        }
    }

    public function update(array $payload, string $id): array
    {
        try {
            $this->anggotaModel->edit($payload, $id);

            $anggota = $this->getById($id);
            return [
                'status' => true,
                'data' => $anggota['data']
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
        DB::beginTransaction();

        try {
            $anggota = AnggotaKeluargaModel::findOrFail($id);
            $anggota->delete();

            DB::commit();
            return true;
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
