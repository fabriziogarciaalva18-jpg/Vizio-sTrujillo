<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        // Datos del pedido
        'user_id',
        'order_number',
        'order_type',
        'delivery_type',           // 'pickup' o 'delivery'
        'status',
        'delivery_date',
        'delivery_time_start',
        'delivery_time_end',

        // Datos de entrega
        'delivery_address',
        'address_lat',             // Latitud de la dirección de entrega
        'address_lng',             // Longitud de la dirección de entrega
        'delivery_distance',       // Distancia calculada (km)
        'district',
        'reference_point',
        'phone',
        'alternative_phone',

        // Financiero
        'subtotal',
        'delivery_fee',            // Dinámico según distancia
        'discount',
        'total',

        // Pago
        'payment_method',
        'payment_status',
        'payment_reference',
        'voucher_path',
        'paid_at',

        // Instrucciones
        'special_instructions',
        'delivery_reference',      // Referencia de entrega (ej: "Dejar en la puerta")
        'admin_notes',

        // Repartidor
        'delivery_person_id',      // ID del usuario repartidor asignado
        'delivery_person_lat',     // Última latitud del repartidor
        'delivery_person_lng',     // Última longitud del repartidor
        'last_location_update',    // Fecha/hora de la última actualización de ubicación
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
        'delivery_person_lat' => 'decimal:8',
        'delivery_person_lng' => 'decimal:8',
        'last_location_update' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // =============================================
    // RELACIONES
    // =============================================

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

    /**
     * Repartidor asignado a este pedido.
     */
    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    // =============================================
    // REGLAS DE NEGOCIO
    // =============================================

    /**
     * Verificar si el pedido puede ser cancelado por el administrador.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Verificar si el usuario puede cancelar el pedido.
     */
    public function canBeCancelledByUser()
    {
        return in_array($this->status, ['pending', 'pending_review']) && $this->payment_status !== 'paid';
    }

    /**
     * Cambiar el estado del pedido.
     */
    public function updateStatus($newStatus)
    {
        $this->update(['status' => $newStatus]);
    }

    // =============================================
    // MÉTODOS DEL REPARTIDOR
    // =============================================

    /**
     * Asignar un repartidor a este pedido y ponerlo en estado 'delivering'.
     */
    public function assignToDelivery($userId)
    {
        $this->update([
            'delivery_person_id' => $userId,
            'status' => 'delivering',
        ]);
    }

    /**
     * Verificar si el pedido está disponible para ser tomado por un repartidor.
     */
    public function isAvailableForDelivery()
    {
        return $this->status === 'preparing' &&
               is_null($this->delivery_person_id) &&
               $this->delivery_date->isToday();
    }

    /**
     * Verificar si el pedido está en estado de entrega y tiene repartidor asignado.
     */
    public function isDelivering()
    {
        return $this->status === 'delivering' && !is_null($this->delivery_person_id);
    }

    /**
     * Obtener la ubicación actual del repartidor (si existe).
     */
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

    /**
     * Actualizar la ubicación del repartidor.
     */
    public function updateDeliveryPersonLocation($lat, $lng)
    {
        $this->update([
            'delivery_person_lat' => $lat,
            'delivery_person_lng' => $lng,
            'last_location_update' => now(),
        ]);
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // =============================================
    // ACCESORS
    // =============================================

    public function getFormattedTotalAttribute()
    {
        return 'S/. ' . number_format($this->total, 2);
    }

    public function getDeliveryFeeAttribute($value)
    {
        return (float) $value;
    }
}
