<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Skrining\PertanyaanHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\PertanyaanRequest;
use App\Http\Resources\Skrining\PertanyaanResource;
use App\Models\PertanyaanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PertanyaanController extends Controller
{
    private $pertanyaan;

    public function __construct()
    {
        $this->pertanyaan = new PertanyaanHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'judul_pertanyaan' => $request->judul_pertanyaan ?? '',
        ];

        $pertanyaans = $this->pertanyaan->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => PertanyaanResource::collection($pertanyaans['data']['data']),
            'meta' => [
                'total' => $pertanyaans['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $pertanyaan = $this->pertanyaan->getById($id);

        if (!$pertanyaan['status']) {
            return response()->failed(['Pertanyaan tidak ditemukan'], 404);
        }

        return response()->success(new PertanyaanResource($pertanyaan['data']));
    }

    public function store(PertanyaanRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['section_id', 'no_urut', 'pertanyaan', 'jenis_jawaban', 'opsi_jawaban']);
        $pertanyaan = $this->pertanyaan->create($payload);

        if (!$pertanyaan['status']) {
            return response()->failed($pertanyaan['error']);
        }

        return response()->success(new PertanyaanResource($pertanyaan['data']), 'Pertanyaan berhasil ditambahkan');
    }

    public function update(PertanyaanRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'section_id', 'no_urut', 'pertanyaan', 'jenis_jawaban', 'opsi_jawaban']);
        $pertanyaan = $this->pertanyaan->update($payload, $payload['id']);

        return response()->success(new PertanyaanResource($pertanyaan['data']), 'Pertanyaan berhasil diubah');
    }

    public function destroy($id)
    {
        $pertanyaan = $this->pertanyaan->delete($id);

        if (!$pertanyaan) {
            return response()->failed(['Mohon maaf pertanyaan tidak ditemukan']);
        }

        return response()->success($pertanyaan, 'Pertanyaan berhasil dihapus');
    }

    public function move(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'direction' => 'required|in:up,down'
            ]);

            $direction = $request->input('direction');

            $current = PertanyaanModel::findOrFail($id);

            if ($direction === 'up') {

                $swap = PertanyaanModel::where('section_id', $current->section_id)
                    ->where('no_urut', '<', $current->no_urut)
                    ->orderBy('no_urut', 'desc')
                    ->first();
            } else {

                $swap = PertanyaanModel::where('section_id', $current->section_id)
                    ->where('no_urut', '>', $current->no_urut)
                    ->orderBy('no_urut', 'asc')
                    ->first();
            }

            if (!$swap) {
                return response()->json([
                    'status' => false,
                    'message' => 'Sudah di posisi paling atas / bawah'
                ]);
            }

            $temp = $current->no_urut;

            $current->update(['no_urut' => $swap->no_urut]);
            $swap->update(['no_urut' => $temp]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Urutan berhasil diperbarui'
            ]);
        } catch (Throwable $th) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
