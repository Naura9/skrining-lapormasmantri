<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Jawaban\KeluargaHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeluargaRequest;
use App\Http\Resources\Jawaban\KeluargaResource;
use Illuminate\Http\Request;

class KeluargaController extends Controller
{
    private $keluarga;

    public function __construct()
    {
        $this->keluarga = new KeluargaHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'nama_keluarga' => $request->nama_keluarga ?? '',
        ];

        $keluargas = $this->keluarga->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => KeluargaResource::collection($keluargas['data']['data']),
            'meta' => [
                'total' => $keluargas['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $keluarga = $this->keluarga->getById($id);

        if (!$keluarga['status']) {
            return response()->failed(['Data keluarga tidak ditemukan'], 404);
        }

        return response()->success(new KeluargaResource($keluarga['data']));
    }

    public function store(KeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['unit_rumah_id', 'no_kk', 'kepala_keluarga']);
        $keluarga = $this->keluarga->create($payload);

        if (!$keluarga['status']) {
            return response()->failed($keluarga['error']);
        }

        return response()->success(new KeluargaResource($keluarga['data']), 'Data keluarga berhasil ditambahkan');
    }

    public function update(KeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'unit_rumah_id', 'no_kk', 'kepala_keluarga']);
        $keluarga = $this->keluarga->update($payload, $payload['id']);

        return response()->success(new KeluargaResource($keluarga['data']), 'Data keluarga berhasil diubah');
    }

    public function destroy($id)
    {
        $keluarga = $this->keluarga->delete($id);

        if (!$keluarga) {
            return response()->failed(['Mohon maaf data keluarga tidak ditemukan']);
        }

        return response()->success($keluarga, 'Data keluarga berhasil dihapus');
    }
}
