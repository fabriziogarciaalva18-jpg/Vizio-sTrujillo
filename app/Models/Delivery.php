<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',           // ✅ Agregado
        'delivery_person_id', // ✅ Agregado
        'delivered_at',       // ✅ Agregado
        'delivery_notes',     // ✅ Agregado
        'status',             // ✅ Agregado
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
