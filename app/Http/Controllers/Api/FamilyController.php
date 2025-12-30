<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyRequest;
use Illuminate\Http\Request;
use App\Helpers\Family\FamilyHelper;
use App\Http\Resources\Family\FamilyResource;

class FamilyController extends Controller
{
    private $family;

    public function __construct()
    {
        $this->family = new FamilyHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'family_card_number' => $request->family_card_number ?? '',
            'neighborhood_rt' => $request->neighborhood_rt ?? '',
            'neighborhood_rw' => $request->neighborhood_rw ?? '',
            'urban_village' => $request->urban_village ?? '',
        ];

        $families = $this->family->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => FamilyResource::collection($families['data']['data']),
            'meta' => [
                'total' => $families['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $family = $this->family->getById($id);

        if (!$family['status']) {
            return response()->failed(['Data keluarga tidak ditemukan'], 404);
        }

        return response()->success(new FamilyResource($family['data']));
    }

    public function store(FamilyRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['family_card_number', 'head_of_family', 'address', 'neighborhood_rt', 'neighborhood_rw', 'urban_village', 'posyandu']);
        $family = $this->family->create($payload);

        if (!$family['status']) {
            return response()->failed($family['error']);
        }

        return response()->success(new FamilyResource($family['data']), 'Data keluarga berhasil ditambahkan');
    }

    public function update(FamilyRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'family_card_number', 'head_of_family', 'address', 'neighborhood_rt', 'neighborhood_rw', 'urban_village', 'posyandu']);
        $family = $this->family->update($payload, $payload['id']);

        if (!$family['status']) {
            return response()->failed($family['error']);
        }

        return response()->success(new FamilyResource($family['data']), 'Data keluarga berhasil diubah');
    }

    public function destroy($id)
    {
        $family = $this->family->delete($id);

        if (!$family) {
            return response()->failed(['Mohon maaf data keluarga tidak ditemukan']);
        }

        return response()->success($family, 'Data keluarga berhasil dihapus');
    }
}
