<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'quantity',
        'unit_price', 'subtotal', 'configuration', 'guests_count', 'catering_items'
    ];

    protected $casts = [
        'configuration' => 'array',
        'catering_items' => 'array',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getConfigurationDescriptionAttribute()
    {
        if (!$this->configuration) return 'Sin personalización';
        
        $description = [];
        
        if (isset($this->configuration['size'])) {
            $description[] = "Tamaño: {$this->configuration['size']}";
        }
        if (isset($this->configuration['layers'])) {
            $description[] = "{$this->configuration['layers']} pisos";
        }
        if (isset($this->configuration['flavor'])) {
            $description[] = "Sabor: {$this->configuration['flavor']}";
        }
        if (isset($this->configuration['message'])) {
            $description[] = "Mensaje: \"{$this->configuration['message']}\"";
        }
        
        return implode(' | ', $description);
    }
}