<?php

namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Models\JawabanModel;
use App\Models\PertanyaanModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class PertanyaanHelper extends Helper
{
    private $pertanyaanModel;

    public function __construct()
    {
        $this->pertanyaanModel = new PertanyaanModel();
    }

    public function getAll(array $filter, int $page = 1, int $itemPerPage = 0, string $sort = '')
    {
        $pertanyaans = $this->pertanyaanModel->getAll($filter, $page, $itemPerPage, $sort);

        return [
            'status' => true,
            'data' => $pertanyaans
        ];
    }

    public function getById(string $id): array
    {
        $pertanyaan = $this->pertanyaanModel->getById($id);
        if (!$pertanyaan) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $pertanyaan
        ];
    }

    public function create(array $payload): array
    {
        DB::beginTransaction();
        try {
            $lastNoUrut = $this->pertanyaanModel
                ->where('section_id', $payload['section_id'])
                ->max('no_urut');

            $payload['no_urut'] = $lastNoUrut ? $lastNoUrut + 1 : 1;

            if (in_array($payload['jenis_jawaban'], ['text', 'textarea', 'date'])) {
                $payload['opsi_jawaban'] = null;
                $payload['opsi_lain'] = 0;
            }

            $pertanyaan = $this->pertanyaanModel->create($payload);

            DB::commit();

            return [
                'status' => true,
                'data' => $pertanyaan
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
            $existing = $this->pertanyaanModel->findOrFail($id);

            $oldSectionId = $existing->section_id;
            $oldNoUrut    = $existing->no_urut;

            if (isset($payload['section_id']) && $payload['section_id'] != $oldSectionId) {

                $newSectionId = $payload['section_id'];

                $this->pertanyaanModel
                    ->where('section_id', $oldSectionId)
                    ->where('no_urut', '>', $oldNoUrut)
                    ->decrement('no_urut');

                $lastNoUrut = $this->pertanyaanModel
                    ->where('section_id', $newSectionId)
                    ->max('no_urut');

                $payload['no_urut'] = $lastNoUrut ? $lastNoUrut + 1 : 1;
            }

            if (in_array($payload['jenis_jawaban'], ['text', 'textarea', 'date'])) {
                $payload['opsi_jawaban'] = null;
            }

            if (in_array($payload['jenis_jawaban'], ['radio', 'checkbox', 'select'])) {
                $payload['opsi_jawaban'] = $payload['opsi_jawaban'] ?? [];
            }

            if (in_array($payload['jenis_jawaban'], ['text', 'textarea', 'date'])) {
                $payload['opsi_jawaban'] = null;
                $payload['opsi_lain'] = 0;
            }

            $existing->update($payload);

            DB::commit();

            return [
                'status' => true,
                'data'   => $this->getById($id)['data']
            ];
        } catch (Throwable $th) {

            DB::rollBack();

            return [
                'status' => false,
                'error'  => $th->getMessage()
            ];
        }
    }

    public function delete(string $id): array
    {
        try {
            $digunakan = JawabanModel::where('pertanyaan_id', $id)->exists();

            if ($digunakan) {
                return [
                    'status' => false,
                    'error' => 'Pertanyaan tidak dapat dihapus karena sudah digunakan dalam skrining'
                ];
            }

            $this->pertanyaanModel->drop($id);

            return [
                'status' => true
            ];
        } catch (\Throwable $th) {

            return [
                'status' => false,
                'error' => $th->getMessage()
            ];
        }
    }
}
