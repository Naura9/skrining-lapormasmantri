<?php

namespace App\Helpers\Screening;

use App\Helpers\Helper;
use App\Models\ScreeningModel;
use Carbon\Carbon;
use Throwable;

class ScreeningHelper extends Helper
{
    private $screeningModel;

    public function __construct()
    {
        $this->screeningModel = new ScreeningModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $screenings = $this->screeningModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $screenings
        ];
    }

    public function getById(string $id): array
    {
        $screening = $this->screeningModel->getById($id);
        if (!$screening) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $screening
        ];
    }

    public function create(array $payload): array
    {
        try {
            $screening = $this->screeningModel->store($payload);

            return [
                'status' => true,
                'data' => $screening
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
            $this->screeningModel->edit($payload, $id);

            $screening = $this->getById($id);
            return [
                'status' => true,
                'data' => $screening['data']
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
            $this->screeningModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getScreeningActivity(array $filter): array
    {
        // Query dasar
        $query = ScreeningModel::with(['user', 'family']);

        // Filter tanggal
        if (!empty($filter['start_date']) && !empty($filter['end_date'])) {
            $query->whereBetween('screening_date', [
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
