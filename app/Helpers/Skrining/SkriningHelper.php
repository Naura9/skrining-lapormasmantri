<?php

namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\JawabanModel;
use App\Models\SkriningModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class SkriningHelper extends Helper
{
    private $skriningModel;

    public function __construct()
    {
        $this->skriningModel = new SkriningModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $skrinings = $this->skriningModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $skrinings
        ];
    }

    public function getById(string $id): array
    {
        $skrining = $this->skriningModel->getById($id);
        if (!$skrining) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $skrining
        ];
    }

    public function createWithJawaban(array $payload): array
    {
        try {
            $skrining = $this->skriningModel->store([
                'keluarga_id' => $payload['keluarga_id'],
                'user_id' => $payload['user_id'],
                'tanggal_skrining' => $payload['tanggal_skrining'],
            ]);

            foreach ($payload['jawaban'] as $jawaban) {
                JawabanModel::create([
                    'skrining_id' => $skrining->id,
                    'pertanyaan_id' => $jawaban['pertanyaan_id'],
                    'anggota_keluarga_id' => $jawaban['anggota_keluarga_id'],
                    'value_jawaban' => $jawaban['value_jawaban'] ?? null,
                ]);
            }

            $data = $this->getById($skrining->id)['data'];

            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function updateWithJawaban(array $payload, string $id): array
    {
        try {
            $this->skriningModel->edit([
                'keluarga_id' => $payload['keluarga_id'],
                'user_id' => $payload['user_id'],
                'tanggal_skrining' => $payload['tanggal_skrining'],
            ], $id);

            DB::table('t_jawaban')->where('skrining_id', $id)->delete();

            foreach ($payload['jawaban'] as $jawaban) {
                DB::table('t_jawaban')->insert([
                    'skrining_id' => $id,
                    'pertanyaan_id' => $jawaban['pertanyaan_id'],
                    'anggota_keluarga_id' => $jawaban['anggota_keluarga_id'],
                    'value_jawaban' => $jawaban['value_jawaban'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $data = $this->getById($id)['data'];

            return [
                'status' => true,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'status' => false,
                'error' => $th->getMessage(),
            ];
        }
    }

    public function delete(string $id): bool
    {
        try {
            $this->skriningModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getSkriningActivity(array $filter): array
    {
        // Query dasar
        $query = SkriningModel::with(['user', 'family']);

        // Filter tanggal
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $query->whereBetween('skrining_date', [
                Carbon::parse($filter['start_date'])->startOfDay(),
                Carbon::parse($filter['end_date'])->endOfDay()
            ]);
        }

        // Filter petugas
        if (!empty($filter['user_id'])) {
            $query->where('user_id', $filter['user_id']);
        }

        // Filter wilayah (opsional)
        if (!empty($filter['neighborhood_rt'])) {
            $query->whereHas('family', function ($q) use ($filter) {
                $q->where('neighborhood_rt', $filter['neighborhood_rt']);
            });
        }

        if (!empty($filter['neighborhood_rw'])) {
            $query->whereHas('family', function ($q) use ($filter) {
                $q->where('neighborhood_rw', $filter['neighborhood_rw']);
            });
        }

        $data = $query->get();

        // Grouping per petugas
        $grouped = $data->groupBy('user.name')->map(function ($items, $namaPetugas) {
            return [
                'nama_petugas' => $namaPetugas,
                'jumlah_skrining' => $items->count(),
            ];
        })->values();

        return [
            'status' => true,
            'periode' => (!empty($filter['start_date']) && !empty($filter['end_date']))
                ? $filter['start_date'] . ' s.d ' . $filter['end_date']
                : 'Semua Periode',
            'total_skrining' => $data->count(),
            'by_petugas' => $grouped
        ];
    }
}
