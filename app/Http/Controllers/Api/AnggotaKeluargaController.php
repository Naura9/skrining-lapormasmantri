<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Warga\AnggotaKeluargaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnggotaKeluargaRequest;
use App\Http\Resources\Warga\AnggotaKeluargaResource;
use Illuminate\Http\Request;

class AnggotaKeluargaController extends Controller
{
    private $anggotaKeluarga;

    public function __construct()
    {
        $this->anggotaKeluarga = new AnggotaKeluargaHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'nama_anggotaKeluarga' => $request->nama_anggotaKeluarga ?? '',
        ];

        $anggotaKeluargas = $this->anggotaKeluarga->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => AnggotaKeluargaResource::collection($anggotaKeluargas['data']['data']),
            'meta' => [
                'total' => $anggotaKeluargas['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $anggotaKeluarga = $this->anggotaKeluarga->getById($id);

        if (!$anggotaKeluarga['status']) {
            return response()->failed(['Data anggota keluarga tidak ditemukan'], 404);
        }

        return response()->success(new AnggotaKeluargaResource($anggotaKeluarga['data']));
    }

    public function store(AnggotaKeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'keluarga_id',
            'nama',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_kk_asal',
            'hubungan_keluarga',
            'status_perkawinan',
            'pendidikan_terakhir',
            'pekerjaan'
        ]);
        $anggotaKeluarga = $this->anggotaKeluarga->create($payload);

        if (!$anggotaKeluarga['status']) {
            return response()->failed($anggotaKeluarga['error']);
        }

        return response()->success(new AnggotaKeluargaResource($anggotaKeluarga['data']), 'Data anggota keluarga berhasil ditambahkan');
    }

    public function update(AnggotaKeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only([
            'id',
            'keluarga_id',
            'nama',
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_kk_asal',
            'hubungan_keluarga',
            'status_perkawinan',
            'pendidikan_terakhir',
            'pekerjaan'
        ]);
        $anggotaKeluarga = $this->anggotaKeluarga->update($payload, $payload['id']);

        return response()->success(new AnggotaKeluargaResource($anggotaKeluarga['data']), 'Data anggota keluarga berhasil diubah');
    }

    public function destroy($id)
    {
        $anggotaKeluarga = $this->anggotaKeluarga->delete($id);

        if (!$anggotaKeluarga) {
            return response()->failed(['Mohon maaf data anggota keluarga tidak ditemukan']);
        }

        return response()->success($anggotaKeluarga, 'Data anggota keluarga berhasil dihapus');
    }
}
