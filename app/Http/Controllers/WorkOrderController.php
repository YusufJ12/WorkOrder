<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderLog;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WorkOrderReportExport;
use App\Exports\RekapitulasiWorkOrderExport;
use App\Exports\HasilTiapOperatorExport;

class WorkOrderController extends Controller
{
    public function getWorkOrderDetail($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        $logs = $workOrder->logs;

        foreach ($logs as $log) {
            if ($log->previous_status == 0 && $log->new_status == 1) {
                $log->detail_status = 'Pending';
            } elseif ($log->previous_status == 1 && $log->new_status == 2) {
                $log->detail_status = 'Pemotongan';
            } elseif ($log->previous_status == 2 && $log->new_status == 3) {
                $log->detail_status = 'Perakitan';
            } elseif ($log->previous_status == 3 && $log->new_status == 4) {
                $log->detail_status = 'Finishing';
            } else {
                $log->detail_status = 'Unknown';
            }

            $startTime = Carbon::parse($log->start_time);
            $endTime = Carbon::parse($log->end_time);
            $durationInSeconds = $endTime->diffInSeconds($startTime);

            $hours = floor($durationInSeconds / 3600);
            $minutes = floor(($durationInSeconds % 3600) / 60);
            $seconds = $durationInSeconds % 60;

            $log->formatted_duration = $this->formatDuration($hours, $minutes, $seconds);
        }

        return response()->json([
            'data' => $logs
        ]);
    }

    private function formatDuration($hours, $minutes, $seconds)
    {
        $duration = '';

        if ($hours > 0) {
            $duration .= $hours . ' jam ';
        }

        if ($minutes > 0) {
            $duration .= $minutes . ' menit ';
        }

        $duration .= $seconds . ' detik';

        return $duration;
    }

    public function export(Request $request)
    {
        $reportType = $request->input('type');

        if ($reportType === 'rekapitulasi') {
            return Excel::download(new RekapitulasiWorkOrderExport(), 'rekapitulasi_work_order.xlsx');
        } elseif ($reportType === 'operator') {
            return Excel::download(new HasilTiapOperatorExport(), 'hasil_tiap_operator.xlsx');
        } else {
            return redirect()->back()->with('error', 'Jenis laporan tidak valid.');
        }
    }
}
