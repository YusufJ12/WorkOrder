<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'previous_status',
        'new_status',
        'start_time',
        'end_time',
        'stage_note',
        'quantity_processed',
        'reject_quantity',
        'updated_by',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }
}
