<?php

namespace App\Helpers\Skrining;

use App\Helpers\Helper;
use App\Helpers\Warga\IdentitasAnggotaHelper;
use App\Helpers\Warga\IdentitasKeluargaHelper;
use App\Models\AnggotaKeluargaModel;
use App\Models\JawabanModel;
use App\Models\KeluargaModel;
use App\Models\SkriningModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    

    public function delete(string $id): bool
    {
        try {
            $this->skriningModel->drop($id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function updateWithJawaban(array $payload, string $id): array
    {
        DB::beginTransaction();

        try {
            $skrining = SkriningModel::findOrFail($id);

            // ---------------------------------
            // 1. UPDATE DATA SKRINING
            // ---------------------------------
            $skrining->update([
                'keluarga_id'      => $payload['keluarga_id'],
                'user_id'          => $payload['user_id'],
                'tanggal_skrining' => $payload['tanggal_skrining'],
            ]);

            // ---------------------------------
            // 2. UPDATE IDENTITAS KELUARGA
            // ---------------------------------
            if (isset($payload['identitas_keluarga'])) {
                $keluarga = KeluargaModel::findOrFail($payload['keluarga_id']);

                $keluarga->update([
                    'alamat'           => $payload['identitas_keluarga']['alamat'] ?? $keluarga->alamat,
                    'rt'               => $payload['identitas_keluarga']['rt'] ?? $keluarga->rt,
                    'rw'               => $payload['identitas_keluarga']['rw'] ?? $keluarga->rw,
                    'is_luar_wilayah'  => $payload['identitas_keluarga']['is_luar_wilayah'] ?? $keluarga->is_luar_wilayah,
                    'alamat_ktp'       => $payload['identitas_keluarga']['alamat_ktp'] ?? $keluarga->alamat_ktp,
                ]);
            }

            // ---------------------------------
            // 3. UPDATE ANGGOTA KELUARGA
            // ---------------------------------
            if (isset($payload['anggota'])) {
                foreach ($payload['anggota'] as $ag) {
                    $anggota = AnggotaKeluargaModel::findOrFail($ag['id']);

                    $anggota->update([
                        'nama'            => $ag['nama'] ?? $anggota->nama,
                        'nik'             => $ag['nik'] ?? $anggota->nik,
                        'tempat_lahir'    => $ag['tempat_lahir'] ?? $anggota->tempat_lahir,
                        'tanggal_lahir'   => $ag['tanggal_lahir'] ?? $anggota->tanggal_lahir,
                        'jenis_kelamin'   => $ag['jenis_kelamin'] ?? $anggota->jenis_kelamin,
                        'hubungan_keluarga' => $ag['hubungan_keluarga'] ?? $anggota->hubungan_keluarga,
                        'status_perkawinan' => $ag['status_perkawinan'] ?? $anggota->status_perkawinan,
                        'pendidikan_terakhir' => $ag['pendidikan_terakhir'] ?? $anggota->pendidikan_terakhir,
                        'pekerjaan'        => $ag['pekerjaan'] ?? $anggota->pekerjaan,
                    ]);
                }
            }

            // ---------------------------------
            // 4. UPDATE JAWABAN SKRINING
            // ---------------------------------

            // Ambil ID jawaban terkini (yang dikirim FE)
            $newAnswerIds = collect($payload['jawaban'])->pluck('id')->filter()->toArray();

            // Hapus jawaban lama yang tidak ada di payload
            JawabanModel::where('skrining_id', $id)
                ->whereNotIn('id', $newAnswerIds)
                ->delete();

            // Update atau insert jawaban baru
            foreach ($payload['jawaban'] as $jwb) {

                JawabanModel::updateOrCreate(
                    [
                        'id' => $jwb['id'] ?? null
                    ],
                    [
                        'id'                    => $jwb['id'] ?? Str::uuid(),
                        'skrining_id'           => $id,
                        'pertanyaan_id'         => $jwb['pertanyaan_id'],
                        'anggota_keluarga_id'   => $jwb['anggota_keluarga_id'] ?? null,
                        'value_jawaban'         => $jwb['value_jawaban'] ?? null,
                    ]
                );
            }

            DB::commit();

            $skrining->load([
                'keluarga.anggota',
                'jawaban.pertanyaan.section.kategori',
            ]);

            return [
                'status' => true,
                'data'   => $skrining,
            ];
        } catch (\Throwable $th) {

            DB::rollBack();

            return [
                'status' => false,
                'error'  => $th->getMessage(),
            ];
        }
    }
}
