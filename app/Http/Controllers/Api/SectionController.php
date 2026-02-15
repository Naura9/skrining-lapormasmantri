<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Skrining\SectionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\Skrining\SectionResource;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    private $section;

    public function __construct()
    {
        $this->section = new SectionHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'judul_section' => $request->judul_section ?? '',
        ];

        $sections = $this->section->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => SectionResource::collection($sections['data']['data']),
            'meta' => [
                'total' => $sections['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $section = $this->section->getById($id);

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
        $section = $this->section->create($payload);

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
        $section = $this->section->update($payload, $payload['id']);

        return response()->success(new SectionResource($section['data']), 'Section berhasil diubah');
    }

    public function destroy($id)
    {
        $result = $this->section->delete($id);

        if (!$result['status']) {
            return response()->failed([$result['message']]);
        }

        return response()->success(null, 'Section berhasil dihapus');
    }
}
