<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Jawaban\UnitHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UnitRequest;
use App\Http\Resources\Jawaban\UnitResource;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    private $unit;

    public function __construct()
    {
        $this->unit = new UnitHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'nama_unit' => $request->nama_unit ?? '',
        ];

        $units = $this->unit->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => UnitResource::collection($units['data']['data']),
            'meta' => [
                'total' => $units['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $unit = $this->unit->getById($id);

        if (!$unit['status']) {
            return response()->failed(['Unit rumah tidak ditemukan'], 404);
        }

        return response()->success(new UnitResource($unit['data']));
    }

    public function store(UnitRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['kelurahan_id', 'posyandu_id', 'alamat', 'rt', 'rw']);
        $unit = $this->unit->create($payload);

        if (!$unit['status']) {
            return response()->failed($unit['error']);
        }

        return response()->success(new UnitResource($unit['data']), 'Unit rumah berhasil ditambahkan');
    }

    public function update(UnitRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'kelurahan_id', 'posyandu_id', 'alamat', 'rt', 'rw']);
        $unit = $this->unit->update($payload, $payload['id']);

        return response()->success(new UnitResource($unit['data']), 'Unit rumah berhasil diubah');
    }

    public function destroy($id)
    {
        $unit = $this->unit->delete($id);

        if (!$unit) {
            return response()->failed(['Mohon maaf unit rumah tidak ditemukan']);
        }

        return response()->success($unit, 'Unit rumah berhasil dihapus');
    }
}
