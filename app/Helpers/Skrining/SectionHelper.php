<?php

namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\KategoriModel;
use App\Models\PertanyaanModel;
use App\Models\SectionModel;
use Illuminate\Support\Facades\DB;
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
        DB::beginTransaction();

        try {
            $kategori = KategoriModel::findOrFail($payload['kategori_id']);

            $lastNoUrut = SectionModel::whereHas('kategori', function ($q) use ($kategori) {
                $q->where('target_skrining', $kategori->target_skrining);
            })->max('no_urut');

            $payload['no_urut'] = $lastNoUrut ? $lastNoUrut + 1 : 1;

            $section = $this->sectionModel->store($payload);

            DB::commit();

            return [
                'status' => true,
                'data' => $section
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

    public function delete(string $id): array
    {
        try {
            $section = $this->sectionModel->getById($id);
            
            if (!$section) {
                return [
                    'status' => false,
                    'message' => 'Section tidak ditemukan'
                ];
            }

            if ($section->pertanyaan()->exists()) {
                return [
                    'status' => false,
                    'message' => 'Section tidak bisa dihapus karena sudah digunakan'
                ];
            }

            $section->delete();

            return [
                'status' => true
            ];
        } catch (\Throwable $th) {

            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }
}
