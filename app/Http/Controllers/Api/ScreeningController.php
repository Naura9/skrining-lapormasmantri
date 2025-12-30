<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScreeningRequest;
use Illuminate\Http\Request;
use App\Helpers\Screening\ScreeningHelper;
use App\Http\Resources\Screening\ScreeningResource;

class ScreeningController extends Controller
{
    private $screening;

    public function __construct()
    {
        $this->screening = new ScreeningHelper();
    }

    public function index(Request $request)
    {
        $filter = [
            'screening_date' => $request->screening_date ?? '',
            'user_id' => $request->user_id ?? '',
        ];

        $screenings = $this->screening->getAll($filter, $request->page ?? 1, $request->per_page ?? 25, $request->sort ?? '');

        return response()->success([
            'list' => ScreeningResource::collection($screenings['data']['data']),
            'meta' => [
                'total' => $screenings['data']['total'],
            ],
        ]);
    }

    public function show($id)
    {
        $screening = $this->screening->getById($id);

        if (!$screening['status']) {
            return response()->failed(['Data skrining tidak ditemukan'], 404);
        }

        return response()->success(new ScreeningResource($screening['data']));
    }

    public function store(ScreeningRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['family_id', 'user_id', 'screening_date']);
        $screening = $this->screening->create($payload);

        if (!$screening['status']) {
            return response()->failed($screening['error']);
        }

        return response()->success(new ScreeningResource($screening['data']), 'Data skrining berhasil ditambahkan');
    }

    public function update(ScreeningRequest $request)
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return response()->failed($request->validator->errors());
        }

        $payload = $request->only(['id', 'family_id', 'user_id', 'screening_date']);
        $screening = $this->screening->update($payload, $payload['id']);

        if (!$screening['status']) {
            return response()->failed($screening['error']);
        }

        return response()->success(new ScreeningResource($screening['data']), 'Data skrining berhasil diubah');
    }

    public function destroy($id)
    {
        $screening = $this->screening->delete($id);

        if (!$screening) {
            return response()->failed(['Mohon maaf data skrining soal tidak ditemukan']);
        }

        return response()->success($screening, 'Data skrining berhasil dihapus');
    }

    public function getScreeningActivity(Request $request)
    {
        $filter = [
            'start_date' => $request->start_date ?? '',
            'end_date' => $request->end_date ?? '',
            'user_id' => $request->user_id ?? '',
            'neighborhood_rt' => $request->neighborhood_rt ?? '',
            'neighborhood_rw' => $request->neighborhood_rw ?? '',
        ];

        $report = $this->screening->getScreeningActivity($filter);

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
}
