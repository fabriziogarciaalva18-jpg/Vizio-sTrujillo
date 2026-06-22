<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'order_type', 'status', 'delivery_date',
        'delivery_time_start', 'delivery_time_end', 'delivery_address', 'district',
        'reference_point', 'phone', 'alternative_phone', 'subtotal', 'delivery_fee',
        'discount', 'total', 'payment_method', 'payment_status', 'special_instructions', 'admin_notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
public function canBeCancelledByUser()
{
    // El usuario puede cancelar si el pedido está pendiente y no ha sido pagado
    return in_array($this->status, ['pending', 'pending_review']) && $this->payment_status !== 'paid';
}
    public function cancellation()
    {
        return $this->hasOne(Cancellation::class);
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function updateStatus($newStatus)
    {
        $this->update(['status' => $newStatus]);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function getFormattedTotalAttribute()
    {
        return 'S/. ' . number_format($this->total, 2);
    }
}
