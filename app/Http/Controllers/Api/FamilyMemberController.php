<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyMemberRequest;
use Illuminate\Http\Request;
use App\Helpers\FamilyMember\FamilyMemberHelper;
use App\Http\Resources\FamilyMember\FamilyMemberResource;

class FamilyMemberController extends Controller
{
    private $familyMember;

    public function __construct()
    {
        $this->familyMember = new FamilyMemberHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'full_name' => $request->full_name ?? '',
        ];

        $families = $this->familyMember->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => FamilyMemberResource::collection($families['data']['data']),
            'meta' => [
                'total' => $families['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $familyMember = $this->familyMember->getById($id);

        if (!$familyMember['status']) {
            return response()->failed(['Data anggota keluarga tidak ditemukan'], 404);
        }

        return response()->success(new FamilyMemberResource($familyMember['data']));
    }

    public function store(FamilyMemberRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['family_id', 'full_name', 'national_id_number', 'place_of_birth', 'date_of_birth', 'gender', 'relationship', 'marital_status', 'last_education', 'occupation']);
        $familyMember = $this->familyMember->create($payload);

        if (!$familyMember['status']) {
            return response()->failed($familyMember['error']);
        }

        return response()->success(new FamilyMemberResource($familyMember['data']), 'Data anggota keluarga berhasil ditambahkan');
    }

    public function update(FamilyMemberRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'family_id', 'full_name', 'national_id_number', 'place_of_birth', 'date_of_birth', 'gender', 'relationship', 'marital_status', 'last_education', 'occupation']);
        $familyMember = $this->familyMember->update($payload, $payload['id']);

        if (!$familyMember['status']) {
            return response()->failed($familyMember['error']);
        }

        return response()->success(new FamilyMemberResource($familyMember['data']), 'Data anggota keluarga berhasil diubah');
    }

    public function destroy($id)
    {
        $familyMember = $this->familyMember->delete($id);

        if (!$familyMember) {
            return response()->failed(['Mohon maaf data anggota keluarga tidak ditemukan']);
        }

        return response()->success($familyMember, 'Data anggota keluarga berhasil dihapus');
    }
}
