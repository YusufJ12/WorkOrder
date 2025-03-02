<?php

namespace App\Exports;

use App\Models\WorkOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class HasilTiapOperatorExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Ambil data hasil tiap operator untuk status "Completed"
        return WorkOrder::select(
            'products.nama_produk',
            'users.name as operator_name',
            DB::raw('SUM(CASE WHEN work_order_logs.new_status = 4 THEN work_order_logs.quantity_processed ELSE 0 END) AS Completed')
        )
            ->join('work_order_logs', 'work_orders.id', '=', 'work_order_logs.work_order_id')
            ->join('products', 'work_orders.product_id', '=', 'products.id')
            ->join('users', 'work_orders.operator_id', '=', 'users.id')
            ->groupBy('products.nama_produk', 'users.name')  // Mengelompokkan berdasarkan produk dan operator
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'Operator',
            'Completed (Operator)'
        ];
    }
}
