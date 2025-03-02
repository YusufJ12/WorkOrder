<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkOrder;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ManagerController extends Controller
{
    public function index()
    {
        return view('manager.index');
    }

    public function getDataManager(Request $request)
    {
        $query = WorkOrder::with('product', 'operator')->latest();

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('deadline') && !empty($request->deadline)) {
            $query->whereDate('production_deadline', $request->deadline);
        }

        return Datatables::of($query)
            ->addColumn('action', function ($workOrder) {
                return '<button class="btn btn-sm btn-primary edit" data-id="' . $workOrder->id . '">
                              <i class="fa fa-edit"></i> Edit
                          </button>
                          <button class="btn btn-sm btn-danger delete" data-id="' . $workOrder->id . '">
                              <i class="fa fa-trash"></i> Delete
                          </button>';
            })
            ->addColumn('product_name', fn($workOrder) => $workOrder->product->nama_produk ?? 'N/A')
            ->addColumn('operator_name', fn($workOrder) => $workOrder->operator->name ?? 'N/A')
            ->addColumn('status_text', function ($workOrder) {
                return match ((int) $workOrder->status) {
                    1 => 'Pending',
                    2 => 'In Progress',
                    3 => 'In Progress',
                    4 => 'Completed',
                    5 => 'Canceled',
                    default => 'Unknown',
                };
            })
            ->make(true);
    }


    public function generateNomor()
    {
        $latestWorkOrder = WorkOrder::latest()->first();
        $nomor = 'WO' . now()->format('ym') . str_pad(($latestWorkOrder ? (int)substr($latestWorkOrder->work_order_number, -4) + 1 : 1), 4, '0', STR_PAD_LEFT);

        return response()->json(['nomer_work_order' => $nomor]);
    }

    public function getNamaProduk()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function getOperator()
    {
        $operators = User::where('type', 3)->get();
        return response()->json($operators);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomer_work_order' => 'required|string|max:255',
            'nama_produk' => 'required|exists:products,id',
            'jumlah' => 'required|integer',
            'deadline' => 'required|date',
            'operator' => 'required|exists:users,id'
        ]);

        DB::beginTransaction();

        try {
            $workOrder = WorkOrder::create([
                'work_order_number' => $validated['nomer_work_order'],
                'product_id' => $validated['nama_produk'],
                'quantity' => $validated['jumlah'],
                'quantity_final' => $validated['jumlah'],
                'production_deadline' => $validated['deadline'],
                'status' => 1, // Status default adalah Pending
                'operator_id' => $validated['operator'],
            ]);

            $workOrder->logs()->create([
                'previous_status' => 0,
                'new_status' => 1,
                'quantity_processed' => $validated['jumlah'],
                'reject_quantity' => 0,
                'stage_note' => 'Work Order dibuat oleh Manager',
                'updated_by' => auth()->id(),
                'start_time' => now(),
                'end_time' => null,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'work_order' => $workOrder]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }


    // Delete Work Order
    public function destroy($id)
    {
        $workOrder = WorkOrder::findOrFail($id);
        $workOrder->delete();

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $workOrder = WorkOrder::findOrFail($id);

        return response()->json([
            'id' => $workOrder->id,
            'nomer_work_order' => $workOrder->work_order_number,
            'nama_produk' => $workOrder->product_id,
            'jumlah' => $workOrder->quantity,
            'deadline' => $workOrder->production_deadline,
            'operator' => $workOrder->operator_id,
            'status' => (int) $workOrder->status,
        ]);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_produk' => 'required|exists:products,id',
            'jumlah' => 'required|integer',
            'deadline' => 'required|date',
            'operator' => 'required|exists:users,id',
            'status' => 'required|integer|in:1,2,3,4'
        ]);

        $workOrder = WorkOrder::findOrFail($id);
        $workOrder->update([
            'product_id' => $validated['nama_produk'],
            'quantity' => $validated['jumlah'],
            'production_deadline' => $validated['deadline'],
            'operator_id' => $validated['operator'],
            'status' => $validated['status'],
        ]);

        return response()->json(['success' => true, 'work_order' => $workOrder]);
    }
}
