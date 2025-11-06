<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'total_amount',
        'status',
        'issue_date',
        'due_date',
    ];

    /**
     * An invoice belongs to an order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
