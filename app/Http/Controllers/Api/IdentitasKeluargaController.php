<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\Warga\IdentitasKeluargaHelper;
use App\Http\Requests\IdentitasKeluargaRequest;
use App\Http\Resources\Warga\IdentitasKeluargaResource;
use Illuminate\Http\Request;

class IdentitasKeluargaController extends Controller
{
    private $helper;

    public function __construct()
    {
        $this->helper = new IdentitasKeluargaHelper();
    }

    public function index(Request $request)
    {
        $data = $this->helper->getAll([
            'kelurahan_id' => $request->kelurahan_id
        ], $request->page ?? 1);

        return response()->success([
            'list' => IdentitasKeluargaResource::collection($data['data']['data']),
            'meta' => [
                'total' => $data['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $data = $this->helper->getById($id);

        if (!$data['status']) {
            return response()->failed(['Data tidak ditemukan'], 404);
        }

        return response()->success(
            new IdentitasKeluargaResource($data['data'])
        );
    }

    public function store(IdentitasKeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        if ($request->has('validate_only')) {
    return response()->json([
        'message' => 'Validasi berhasil'
    ]);
}
        $result = $this->helper->create($request->validated());

        if (!$result['status']) {
            return response()->failed($result['error']);
        }

        return response()->success(
            new IdentitasKeluargaResource($result['data']),
            'Identitas berhasil ditambahkan'
        );
    }

    public function update(IdentitasKeluargaRequest $request, $id)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $result = $this->helper->update($request->validated(), $id);

        if (!$result['status']) {
            return response()->failed($result['error']);
        }

        return response()->success(
            new IdentitasKeluargaResource($result['data']),
            'Identitas berhasil diubah'
        );
    }

    public function destroy($id)
    {
        $deleted = $this->helper->delete($id);

        if (!$deleted) {
            return response()->failed(['Gagal menghapus data']);
        }

        return response()->success(true, 'Identitas berhasil dihapus');
    }

    public function validateOnly(IdentitasKeluargaRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors'  => $request->validator->errors()
            ], 422);
        }

        return response()->json([
            'message' => 'Valid'
        ]);
    }
}
