<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KelurahanRequest;
use Illuminate\Http\Request;
use App\Helpers\Kelurahan\KelurahanHelper;
use App\Http\Resources\Kelurahan\KelurahanResource;

class KelurahanController extends Controller
{
    private $kelurahanHelper;

    public function __construct()
    {
        $this->kelurahanHelper = new KelurahanHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'nama_kelurahan' => $request->nama_kelurahan ?? '',
            'nama_posyandu' => $request->nama_posyandu ?? '',
        ];

        $kelurahans = $this->kelurahanHelper->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => KelurahanResource::collection($kelurahans['data']),
            'meta' => [
                'total' => $kelurahans['total'],
                'current_page' => $kelurahans['data']->currentPage(),
                'per_page' => $kelurahans['data']->perPage(),
                'last_page' => $kelurahans['data']->lastPage(),
            ],
        ]);
    }

    public function show($id)
    {
        $kelurahan = $this->kelurahanHelper->getById($id);

        if (!$kelurahan['status']) {
            return response()->failed(['Data kelurahan tidak ditemukan'], 404);
        }

        return response()->success(new KelurahanResource($kelurahan['data']));
    }

    public function store(KelurahanRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['nama_kelurahan', 'posyandu']);
        $kelurahan = $this->kelurahanHelper->create($payload);

        if (!$kelurahan['status']) {
            return response()->failed($kelurahan['error']);
        }

        return response()->success(new KelurahanResource($kelurahan['data']), 'Kelurahan berhasil ditambahkan');
    }

    public function update(KelurahanRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'nama_kelurahan', 'posyandu', 'posyandu_deleted']);
        $kelurahan = $this->kelurahanHelper->update($payload, $payload['id']);

        if (!$kelurahan['status']) {
            return response()->failed($kelurahan['error']);
        }

        return response()->success(new KelurahanResource($kelurahan['data']), 'Kelurahan berhasil diubah');
    }

    public function destroy($id)
    {
        $result = $this->kelurahanHelper->delete($id);

        if (!$result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => $result['message']
        ]);
    }
}
