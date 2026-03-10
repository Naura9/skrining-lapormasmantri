<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Monitoring\MonitoringHelper;

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
            'siklus_id'
        ]);

        $result = $this->monitoringHelper->monitoringHasilSkrining($filter);

        return response()->json($result);
    }
}
