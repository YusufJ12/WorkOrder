<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use Yajra\DataTables\DataTables;
use App\Models\WorkOrderLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OperatorController extends Controller
{
    public function index()
    {
        return view('operator.index');
    }

    public function getDataOperator(Request $request)
    {
        $user = auth()->user();

        $query = WorkOrder::with('product', 'operator')->where('operator_id', $user->id)->latest();

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        return DataTables::of($query)
            ->addColumn('action', function ($workOrder) use ($user) {
                if ($user->id == $workOrder->operator_id) {
                    $nextStatus = $workOrder->status + 1;
                    return '<button class="btn btn-sm btn-info update-status" 
                            data-id="' . $workOrder->id . '" data-next-status="' . $nextStatus . '">
                            Ubah ke Status Selanjutnya
                        </button>';
                }
                return '<span class="text-muted">No Action</span>';
            })
            ->addColumn('product_name', fn($workOrder) => $workOrder->product->nama_produk ?? 'N/A')
            ->addColumn('operator_name', fn($workOrder) => $workOrder->operator->name ?? 'N/A')
            ->make(true);
    }

    public function getCounts()
    {
        $user = auth()->user();

        return response()->json([
            'pending' => WorkOrder::where('status', 1)->where('operator_id', $user->id)->count(),
            'pemotongan' => WorkOrder::where('status', 2)->where('operator_id', $user->id)->count(),
            'perakitan' => WorkOrder::where('status', 3)->where('operator_id', $user->id)->count(),
            'completed' => WorkOrder::where('status', 4)->where('operator_id', $user->id)->count(),
            'canceled' => WorkOrder::where('status', 5)->where('operator_id', $user->id)->count()
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status'      => 'required|integer|in:1,2,3,4,5',
            'quantity'    => 'required|integer|min:1',
            'stage_note'  => 'nullable|string',
            'note'        => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $workOrder = WorkOrder::findOrFail($id);
            $previousStatus = $workOrder->status;
            $newStatus = $validated['status'];
            $quantity = $validated['quantity'];

            if ($newStatus == 5) {
                $rejectQuantity = $workOrder->quantity_final;
            } else {
                $rejectQuantity = $workOrder->quantity_final - $quantity;
            }

            $newQuantityFinal = $quantity;
            $stageNote = $validated['stage_note'];

            WorkOrderLog::where('work_order_id', $workOrder->id)
                ->where('new_status', $previousStatus)
                ->whereNull('end_time')
                ->update(['end_time' => now()]);

            $startTime = null;
            $endTime = null;

            if ($newStatus == 2 || $newStatus == 3) {
                $startTime = WorkOrderLog::where('work_order_id', $workOrder->id)
                    ->where('new_status', $newStatus)
                    ->whereNotNull('start_time')
                    ->exists() ? null : now();
            }

            if ($newStatus == 4) {
                $startTime = WorkOrderLog::where('work_order_id', $workOrder->id)
                    ->where('new_status', $newStatus)
                    ->whereNotNull('start_time')
                    ->exists() ? null : now();
                $endTime = now();
            }

            if ($newStatus == 5) {
                $endTime = now();
            }

            WorkOrderLog::create([
                'work_order_id'      => $workOrder->id,
                'previous_status'    => $previousStatus,
                'new_status'         => $newStatus,
                'stage_note'         => $stageNote ?? null,
                'updated_by'         => auth()->id(),
                'start_time'         => $startTime,
                'end_time'           => $endTime,
                'quantity_processed' => $quantity,
                'reject_quantity'    => $rejectQuantity,
            ]);

            $workOrder->update([
                'status'        => $newStatus,
                'quantity_final' => $newQuantityFinal,
                'note'          => $validated['note'] ?? $workOrder->note,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
