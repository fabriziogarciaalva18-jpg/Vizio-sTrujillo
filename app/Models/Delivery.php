<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',           // ✅ Agregar
        'delivery_person_id', // ✅ Agregar
        'delivered_at',       // ✅ Agregar
        'delivery_notes',     // ✅ Agregar
        'status',             // ✅ Agregar
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
