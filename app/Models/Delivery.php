<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
    'order_id',
    'delivery_person_id',
    'delivered_at',
    'delivery_notes',
    'status',
    'scheduled_date',        // ✅ Agregado
    'scheduled_time_start',  // ✅ Agregado
    'scheduled_time_end',    // ✅ Agregado
];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }
}
