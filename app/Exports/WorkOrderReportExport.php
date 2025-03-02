<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\HasilTiapOperatorExport;
use App\Exports\RekapitulasiWorkOrderExport;

class WorkOrderReportExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Rekapitulasi Work Order' => new RekapitulasiWorkOrderExport(),
            'Hasil Tiap Operator' => new HasilTiapOperatorExport(),
        ];
    }
}
