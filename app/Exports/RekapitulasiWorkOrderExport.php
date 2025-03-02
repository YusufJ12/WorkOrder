<?php

namespace App\Exports;

use App\Models\WorkOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class RekapitulasiWorkOrderExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Ambil data rekapitulasi berdasarkan produk dan status
        return WorkOrder::select(
            'products.nama_produk',
            DB::raw('SUM(CASE WHEN work_order_logs.previous_status = 0 AND work_order_logs.new_status = 1 THEN work_order_logs.quantity_processed ELSE 0 END) AS Pending'),
            DB::raw('SUM(CASE WHEN work_order_logs.previous_status = 1 AND work_order_logs.new_status = 2 THEN work_order_logs.quantity_processed ELSE 0 END) AS `InProgress Potong`'),  // Alias menggunakan backticks
            DB::raw('SUM(CASE WHEN work_order_logs.previous_status = 2 AND work_order_logs.new_status = 3 THEN work_order_logs.quantity_processed ELSE 0 END) AS `InProgress Rakit`'),  // Alias menggunakan backticks
            DB::raw('SUM(CASE WHEN work_order_logs.previous_status = 3 AND work_order_logs.new_status = 4 THEN work_order_logs.quantity_processed ELSE 0 END) AS Completed'),
            DB::raw('SUM(CASE WHEN work_order_logs.previous_status = 3 AND work_order_logs.new_status = 5 THEN work_order_logs.quantity_processed ELSE 0 END) AS Canceled')
        )
            ->join('work_order_logs', 'work_orders.id', '=', 'work_order_logs.work_order_id')
            ->join('products', 'work_orders.product_id', '=', 'products.id')
            ->groupBy('products.nama_produk')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'Pending',
            'In Progress Potong',
            'In Progress Rakit',
            'Completed',
            'Canceled'
        ];
    }
}
