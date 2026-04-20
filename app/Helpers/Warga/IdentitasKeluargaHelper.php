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

        if (!empty($filter['posyandu_id'])) {
            $query->where('posyandu_id', $filter['posyandu_id']);
        }

        if (!empty($filter['keyword'])) {
            $query->whereHas('keluarga', function ($q) use ($filter) {
                $q->where('no_kk', 'like', "%{$filter['keyword']}%")
                    ->orWhereHas('kepalaKeluarga', function ($q2) use ($filter) {
                        $q2->where('nama', 'like', "%{$filter['keyword']}%");
                    });
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

        $user = auth()->user();

        if ($user->role === 'kader') {
            $payload['kelurahan_id'] = optional($user->kaderDetail->posyandu->kelurahan)->id;
            $payload['posyandu_id']  = optional($user->kaderDetail->posyandu)->id;
        }
        
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

            $unit->update([
                'kelurahan_id' => $payload['kelurahan_id'],
                'posyandu_id'  => $payload['posyandu_id'],
                'alamat'       => $payload['alamat'],
                'rt'           => $payload['rt'],
                'rw'           => $payload['rw'],
            ]);

            $existingIds = $unit->keluarga()->pluck('id')->toArray();
            $incomingIds = collect($payload['keluarga'])
                ->pluck('id')
                ->filter()
                ->toArray();

            $idsToDelete = array_diff($existingIds, $incomingIds);

            if (!empty($idsToDelete)) {
                AnggotaKeluargaModel::whereIn('keluarga_id', $idsToDelete)->delete();

                KeluargaModel::whereIn('id', $idsToDelete)->delete();
            }
            foreach ($payload['keluarga'] as $item) {
                $keluarga = KeluargaModel::find($item['id'] ?? null);

                if ($keluarga) {

                    $resetKTP = (
                        $keluarga->is_luar_wilayah == true &&
                        $item['is_luar_wilayah'] == false
                    );

                    $keluarga->update([
                        'is_luar_wilayah' => $item['is_luar_wilayah'],
                        'alamat_ktp'      => $resetKTP ? null : $item['alamat_ktp'],
                        'rt_ktp'          => $resetKTP ? null : $item['rt_ktp'],
                        'rw_ktp'          => $resetKTP ? null : $item['rw_ktp'],
                        'no_telepon'      => $item['no_telepon'],
                    ]);

                    $kepala = $keluarga->kepalaKeluarga;
                    $kepala->update([
                        'nama' => $item['nama_kepala_keluarga'],
                        'nik'  => $item['nik_kepala_keluarga'],
                    ]);
                } else {
                    $keluarga = KeluargaModel::create([
                        'unit_rumah_id'   => $unit->id,
                        'no_kk'           => $item['no_kk'],
                        'is_luar_wilayah' => $item['is_luar_wilayah'],
                        'alamat_ktp'      => $item['is_luar_wilayah'] ? $item['alamat_ktp'] : null,
                        'rt_ktp'          => $item['is_luar_wilayah'] ? $item['rt_ktp'] : null,
                        'rw_ktp'          => $item['is_luar_wilayah'] ? $item['rw_ktp'] : null,
                        'no_telepon'      => $item['no_telepon'],
                    ]);

                    AnggotaKeluargaModel::create([
                        'keluarga_id' => $keluarga->id,
                        'nama'        => $item['nama_kepala_keluarga'],
                        'nik'         => $item['nik_kepala_keluarga'],
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
