<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use Illuminate\Http\Request;
use App\Helpers\Kategori\KategoriHelper;
use App\Http\Resources\Kategori\KategoriResource;

class KategoriController extends Controller
{
    private $kategori;

    public function __construct()
    {
        $this->kategori = new KategoriHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'nama_kategori' => $request->nama_kategori ?? '',
        ];

        $kategoris = $this->kategori->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => KategoriResource::collection($kategoris['data']['data']),
            'meta' => [
                'total' => $kategoris['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $kategori = $this->kategori->getById($id);

        if (!$kategori['status']) {
            return response()->failed(['Kategori skrining tidak ditemukan'], 404);
        }

        return response()->success(new KategoriResource($kategori['data']));
    }

    public function store(KategoriRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['nama_kategori', 'target_skrining']);
        $kategori = $this->kategori->create($payload);

        if (!$kategori['status']) {
            return response()->failed($kategori['error']);
        }

        return response()->success(new KategoriResource($kategori['data']), 'Kategori skrining berhasil ditambahkan');
    }

    public function update(KategoriRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'nama_kategori', 'target_skrining']);
        $kategori = $this->kategori->update($payload, $payload['id']);

        return response()->success(new KategoriResource($kategori['data']), 'Kategori skrining berhasil diubah');
    }

    public function destroy($id)
    {
        $kategori = $this->kategori->delete($id);

        if (!$kategori) {
            return response()->failed(['Mohon maaf kategori skrining tidak ditemukan']);
        }

        return response()->success($kategori, 'Kategori skrining berhasil dihapus');
    }
}
