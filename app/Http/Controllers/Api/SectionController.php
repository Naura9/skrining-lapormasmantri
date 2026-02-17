<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Skrining\SectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\Skrining\SectionResource;
use App\Models\SectionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SectionController extends Controller
{
    private $sectionHelper;
    private $sectionModel;

    public function __construct()
    {
        $this->sectionHelper = new SectionHelper();
        $this->sectionModel = new SectionModel();
    }

    public function index(Request $request)
    {
        $filter = [
            'judul_section' => $request->judul_section ?? '',
        ];

        $sections = $this->sectionHelper->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => SectionResource::collection($sections['data']['data']),
            'meta' => [
                'total' => $sections['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $section = $this->sectionHelper->getById($id);

        if (!$section['status']) {
            return response()->failed(['Section tidak ditemukan'], 404);
        }

        return response()->success(new SectionResource($section['data']));
    }

    public function store(SectionRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['kategori_id', 'judul_section', 'no_urut']);
        $section = $this->sectionHelper->create($payload);

        if (!$section['status']) {
            return response()->failed($section['error']);
        }

        return response()->success(new SectionResource($section['data']), 'Section berhasil ditambahkan');
    }

    public function update(SectionRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'kategori_id', 'judul_section', 'no_urut']);
        $section = $this->sectionHelper->update($payload, $payload['id']);

        return response()->success(new SectionResource($section['data']), 'Section berhasil diubah');
    }

    public function destroy($id)
    {
        $result = $this->sectionHelper->delete($id);

        if (!$result['status']) {
            return response()->failed([$result['message']]);
        }

        return response()->success(null, 'Section berhasil dihapus');
    }

    public function move(Request $request, string $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'direction' => 'required|in:up,down'
            ]);

            $direction = $request->input('direction');

            $current = $this->sectionModel->findOrFail($id);

            if ($direction === 'up') {

                $swap = $this->sectionModel
                    ->where('kategori_id', $current->kategori_id)
                    ->where('no_urut', '<', $current->no_urut)
                    ->orderBy('no_urut', 'desc')
                    ->first();
            } else {

                $swap = $this->sectionModel
                    ->where('kategori_id', $current->kategori_id)
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

            return response()->json(['status' => true]);
        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
