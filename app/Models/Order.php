<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'delivery_reference',
        'order_type',
        'delivery_type',      // ✅ NUEVO: 'pickup' o 'delivery'
        'status',
        'delivery_date',
        'delivery_time_start',
        'delivery_time_end',
        'delivery_address',
        'address_lat',        // ✅ NUEVO: latitud de la dirección
        'address_lng',        // ✅ NUEVO: longitud de la dirección
        'delivery_distance',  // ✅ NUEVO: distancia en km
        'district',
        'reference_point',
        'phone',
        'alternative_phone',
        'subtotal',
        'delivery_fee',       // ✅ AHORA es dinámico (se calcula según distancia)
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'special_instructions',
        'admin_notes',
        'paid_at',
        'voucher_path',
        'payment_reference',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_date' => 'date',
        'address_lat' => 'decimal:8',
        'address_lng' => 'decimal:8',
        'delivery_distance' => 'decimal:2',
        'paid_at' => 'datetime',
    ];
public function deliveryPerson()
{
    return $this->belongsTo(User::class, 'delivery_person_id');
}

// Verificar si el pedido tiene un repartidor asignado y está en camino
public function isDelivering()
{
    return $this->status === 'delivering' && $this->delivery_person_id;
}

// Obtener la ubicación actual del repartidor (si existe)
public function getDeliveryPersonLocationAttribute()
{
    if ($this->delivery_person_lat && $this->delivery_person_lng) {
        return [
            'lat' => $this->delivery_person_lat,
            'lng' => $this->delivery_person_lng,
            'updated_at' => $this->last_location_update,
        ];
    }
    return null;
}

// Actualizar ubicación del repartidor
public function updateDeliveryPersonLocation($lat, $lng)
{
    $this->update([
        'delivery_person_lat' => $lat,
        'delivery_person_lng' => $lng,
        'last_location_update' => now(),
    ]);
}
    // Relaciones
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

    public function cancellation()
    {
        return $this->hasOne(Cancellation::class);
    }

    // Reglas de negocio
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeCancelledByUser()
    {
        return in_array($this->status, ['pending', 'pending_review']) && $this->payment_status !== 'paid';
    }

    public function updateStatus($newStatus)
    {
        $this->update(['status' => $newStatus]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accesor para mostrar el total formateado
    public function getFormattedTotalAttribute()
    {
        return 'S/. ' . number_format($this->total, 2);
    }

    // 🔥 Opcional: método para obtener el costo de envío basado en la distancia (si se guarda)
    public function getDeliveryFeeAttribute($value)
    {
        return (float) $value;
    }
}
