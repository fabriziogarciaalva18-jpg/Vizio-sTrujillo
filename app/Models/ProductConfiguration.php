<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_type',
        'name',
        'price_modifier',
        'sort_order',
        'is_active',
        // Eliminar 'product_id' del fillable porque ya no se usa
    ];

    protected $casts = [
        'price_modifier' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relación muchos a muchos con productos
    public function products()
    {
        return $this->belongsToMany(Product::class, 'configuration_product');
    }

    // Scope para obtener configuraciones activas
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope por tipo
    public function scopeByType($query, $type)
    {
        return $query->where('config_type', $type);
    }
}
