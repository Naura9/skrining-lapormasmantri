<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkriningRequest;
use Illuminate\Http\Request;
use App\Helpers\Skrining\SkriningHelper;
use App\Http\Resources\Skrining\SkriningResource;

class SkriningController extends Controller
{
    private $skriningHelper;

    public function __construct()
    {
        $this->skriningHelper= new SkriningHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'keluarga_id' => $request->keluarga_id ?? '',
            'user_id' => $request->user_id ?? '',
            'tanggal_skrining' => $request->tanggal_skrining ?? ''
        ];

        $skrinings = $this->skriningHelper->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => SkriningResource::collection($skrinings['data']['data']),
            'meta' => [
                'total' => $skrinings['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $skrining = $this->skriningHelper->getById($id);

        if (!$skrining['status']) {
            return response()->failed(['Data skrining tidak ditemukan'], 404);
        }

        return response()->success(new SkriningResource($skrining['data']));
    }

    public function store(SkriningRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $skrining = $this->skriningHelper->createWithJawaban($request->validated());

        if (!$skrining['status']) {
            return response()->failed($skrining['error']);
        }

        return response()->success(new SkriningResource($skrining['data']), 'Skrining berhasil ditambahkan');
    }

    public function update(SkriningRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'keluarga_id', 'user_id', 'tanggal_skrining', 'jawaban']);
        $skrining = $this->skriningHelper->updateWithJawaban($payload, $payload['id']);

        if (!$skrining['status']) {
            return response()->failed($skrining['error']);
        }

        return response()->success(new SkriningResource($skrining['data']), 'Skrining berhasil diubah');
    }

    public function destroy($id)
    {
        $skrining = $this->skriningHelper->delete($id);

        if (!$skrining) {
            return response()->failed(['Skrining tidak ditemukan']);
        }

        return response()->success($skrining, 'Skrining berhasil dihapus');
    }
}
