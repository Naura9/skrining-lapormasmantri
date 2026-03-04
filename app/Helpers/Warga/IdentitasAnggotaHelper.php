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
    public function getAll(array $filter, int $page = 1, int $perPage = 25)
    {
        $query = AnggotaKeluargaModel::with('keluarga.kepalaKeluarga');

        if (!empty($filter['no_kk'])) {
            $query->whereHas('keluarga', function ($q) use ($filter) {
                $q->where('no_kk', $filter['no_kk']);
            });
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function getById(string $id): array
    {
        $unit = UnitModel::with([
            'keluarga.kepalaKeluarga'
        ])->find($id);

        if (!$unit) {
            return [
                'status' => false,
                'data' => null
            ];
        }

        return [
            'status' => true,
            'data' => $unit
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

            // 🔥 CEK APAKAH NIK SUDAH ADA
            $existing = AnggotaKeluargaModel::where('nik', $payload['nik'])->first();

            if ($existing) {

                // 🔥 UPDATE jika sudah ada
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

            // 🔥 CREATE jika belum ada
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
        DB::beginTransaction();

        try {

            $unit = UnitModel::findOrFail($id);

            $unit->update([
                'kelurahan_id' => $payload['kelurahan_id'],
                'posyandu_id'  => $payload['posyandu_id'],
                'alamat'       => $payload['alamat'],
                'rt'           => $payload['rt'],
                'rw'           => $payload['rw'],
            ]);

            foreach ($unit->keluarga as $kel) {
                AnggotaKeluargaModel::where('keluarga_id', $kel->id)->delete();
                $kel->delete();
            }

            foreach ($payload['keluarga'] as $item) {

                $nik  = $item['nik_kepala_keluarga'];
                $nama = $item['nama_kepala_keluarga'];

                unset($item['nik_kepala_keluarga'], $item['nama_kepala_keluarga']);

                $item['unit_rumah_id'] = $unit->id;

                $keluarga = KeluargaModel::create($item);

                AnggotaKeluargaModel::create([
                    'keluarga_id' => $keluarga->id,
                    'nama' => $nama,
                    'nik' => $nik,
                    'hubungan_keluarga' => 'Kepala Keluarga'
                ]);
            }

            DB::commit();

            return [
                'status' => true,
                'data' => $unit->fresh('keluarga.kepalaKeluarga')
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
        DB::beginTransaction();

        try {

            $unit = UnitModel::findOrFail($id);

            foreach ($unit->keluarga as $kel) {
                AnggotaKeluargaModel::where('keluarga_id', $kel->id)->delete();
                $kel->delete();
            }

            $unit->delete();

            DB::commit();

            return true;
        } catch (Throwable $th) {
            DB::rollBack();
            return false;
        }
    }
}
