<?php

namespace App\Http\Controllers\Api;

use App\Exports\ReportHasilSkrining;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Monitoring\MonitoringHelper;
use App\Http\Requests\UpdateSkriningRequest;
use Maatwebsite\Excel\Facades\Excel;

class MonitoringController extends Controller
{
    private $monitoringHelper;

    public function __construct()
    {
        $this->monitoringHelper = new MonitoringHelper();
    }

    public function monitoringKader(Request $request)
    {
        $filter = $request->only([
            'search',
            'kelurahan_id',
            'posyandu_id'
        ]);

        $result = $this->monitoringHelper->monitoringKader($filter);

        return response()->json($result);
    }

    public function monitoringNikPerKk(Request $request)
    {
        $filter = $request->only([
            'kelurahan_id',
            'posyandu_id'
        ]);

        $result = $this->monitoringHelper->monitoringNikPerKk($filter);

        return response()->json($result);
    }

    public function monitoringNikPerSiklus(Request $request)
    {
        $filter = $request->only([
            'siklus_id',
            'kelurahan_id',
            'posyandu_id',
            'sort'
        ]);

        $result = $this->monitoringHelper->monitoringNikPerSiklus($filter);

        return response()->json($result);
    }

    public function monitoringHasilSkrining(Request $request)
    {
        $filter = $request->only([
            'kelurahan_id',
            'posyandu_id',
            'search'
        ]);

        $result = $this->monitoringHelper->monitoringHasilSkrining($filter);

        return response()->json($result);
    }


    public function exportHasilSkrining(Request $request)
    {
        $filter = $request->only([
            'kelurahan_id',
            'posyandu_id',
            'siklus_id',
            'search'
        ]);

        $result = $this->monitoringHelper->monitoringHasilSkrining($filter);

        return Excel::download(
            new ReportHasilSkrining($result['data']),
            'report-hasil-skrining.xlsx'
        );
    }

    public function chartHasilSkrining(Request $request)
    {
        $filter = $request->only([
            'kelurahan_id',
            'posyandu_id',
            'search'
        ]);

        $result = $this->monitoringHelper->chartHasilSkrining($filter);

        return response()->json($result);
    }

    public function updateUnit(UpdateSkriningRequest $request, $unitId)
    {
        try {
            $result = $this->monitoringHelper->updateUnitSkrining($unitId, $request->validated());

            return response()->json([
                "status" => true,
                "message" => "Data skrining berhasil diperbarui",
                "data" => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
