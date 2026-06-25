<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Skrining\TargetHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TargetRequest;
use App\Http\Resources\Skrining\TargetResource;
use App\Models\KategoriModel;
use Illuminate\Http\Request;

class TargetController extends Controller
{
    private $target;

    public function __construct()
    {
        $this->target = new TargetHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'nama_target' => $request->nama_target ?? '',
        ];

        $targets = $this->target->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => TargetResource::collection($targets['data']['data']),
            'meta' => [
                'total' => $targets['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $target = $this->target->getById($id);

        if (!$target['status']) {
            return response()->failed(['Target tidak ditemukan'], 404);
        }

        return response()->success(new TargetResource($target['data']));
    }

    public function store(TargetRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['kelurahan_id', 'kategori_id', 'target']);
        $target = $this->target->create($payload);

        if (!$target['status']) {
            return response()->failed($target['error']);
        }

        return response()->success(new TargetResource($target['data']), 'Data berhasil disimpan!');
    }

    public function update(TargetRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'kelurahan_id', 'kategori_id', 'target']);
        $target = $this->target->update($payload, $payload['id']);

        return response()->success(new TargetResource($target['data']), 'Data berhasil disimpan!');
    }

    public function destroy($id)
    {
        $target = $this->target->delete($id);

        if (!$target) {
            return response()->failed(['Mohon maaf target tidak ditemukan']);
        }

        return response()->success($target, 'Data berhasil dihapus!');
    }

    public function showByKelurahan($kelurahanId, $kategoriId)
    {
        return $this->target->getByKelurahan($kelurahanId, $kategoriId);
    }
}
