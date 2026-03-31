<?php

namespace App\Helpers\Warga;

use App\Helpers\Helper;
use App\Models\UnitModel;
use App\Models\KeluargaModel;
use App\Models\AnggotaKeluargaModel;
use Illuminate\Support\Facades\DB;
use Throwable;

class IdentitasKeluargaHelper extends Helper
{
    public function getAll(array $filter, int $page = 1, int $perPage = 25)
    {
        $query = UnitModel::with([
            'keluarga.kepalaKeluarga'
        ]);

        if (!empty($filter['kelurahan_id'])) {
            $query->where('kelurahan_id', $filter['kelurahan_id']);
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
            $unit = UnitModel::create([
                'kelurahan_id' => $payload['kelurahan_id'],
                'posyandu_id'  => $payload['posyandu_id'],
                'alamat'       => $payload['alamat'],
                'rt'           => $payload['rt'],
                'rw'           => $payload['rw'],
            ]);

            foreach ($payload['keluarga'] as $item) {

                $nik  = $item['nik_kepala_keluarga'];
                $nama = $item['nama_kepala_keluarga'];

                unset(
                    $item['nik_kepala_keluarga'],
                    $item['nama_kepala_keluarga']
                );

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
                'data' => $unit->load('keluarga.kepalaKeluarga')
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
        $unit = UnitModel::findOrFail($id);

        // update unit
        $unit->update([
            'kelurahan_id' => $payload['kelurahan_id'],
            'posyandu_id'  => $payload['posyandu_id'],
            'alamat'       => $payload['alamat'],
            'rt'           => $payload['rt'],
            'rw'           => $payload['rw'],
        ]);

        foreach ($payload['keluarga'] as $item) {

            // cari keluarga lama
            $keluarga = KeluargaModel::where('unit_rumah_id', $unit->id)
                ->where('no_kk', $item['no_kk'])
                ->first();

            if ($keluarga) {
                // update keluarga
                $keluarga->update([
                    'alamat_ktp' => $item['alamat_ktp'],
                    'rt_ktp'     => $item['rt_ktp'],
                    'rw_ktp'     => $item['rw_ktp'],
                    'no_telepon' => $item['no_telepon'],
                ]);

                // update kepala keluarga
                $kepala = $keluarga->kepalaKeluarga;
                $kepala->update([
                    'nama' => $item['nama_kepala_keluarga'],
                    'nik'  => $item['nik_kepala_keluarga'],
                ]);

            } else {

                // create baru kalau tidak ditemukan
                $keluarga = KeluargaModel::create([
                    'unit_rumah_id' => $unit->id,
                    'no_kk'         => $item['no_kk'],
                    'alamat_ktp'    => $item['alamat_ktp'],
                    'rt_ktp'        => $item['rt_ktp'],
                    'rw_ktp'        => $item['rw_ktp'],
                    'no_telepon'    => $item['no_telepon'],
                ]);

                AnggotaKeluargaModel::create([
                    'keluarga_id' => $keluarga->id,
                    'nama' => $item['nama_kepala_keluarga'],
                    'nik'  => $item['nik_kepala_keluarga'],
                    'hubungan_keluarga' => 'Kepala Keluarga'
                ]);
            }
        }

        DB::commit();

        return [
            'status' => true,
            'data'   => $unit->fresh('keluarga.kepalaKeluarga')
        ];

    } catch (Throwable $th) {
        DB::rollBack();
        return [
            'status' => false,
            'error'  => $th->getMessage()
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