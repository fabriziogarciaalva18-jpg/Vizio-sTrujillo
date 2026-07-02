<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image_url',
        'product_type',
        'has_sizes',
        'has_layers',
        'has_flavors',
        'has_fillings',
        'has_coverings',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'has_sizes' => 'boolean',
        'has_layers' => 'boolean',
        'has_flavors' => 'boolean',
        'has_fillings' => 'boolean',
        'has_coverings' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function configurations()
{
    return $this->belongsToMany(ProductConfiguration::class, 'configuration_product');
}

   public function getConfigurationsByType($type)
{
    return $this->configurations()
        ->where('config_type', $type)
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
}

    // ACCESOR CORREGIDO - Usa $this->attributes en lugar de propiedad directa
    public function getGroupedConfigurationsAttribute()
{
    $grouped = [];
    $types = ['size', 'layers', 'flavor', 'filling', 'covering', 'shape', 'color', 'toppings', 'decoration', 'message'];

    foreach ($types as $type) {
        $items = $this->getConfigurationsByType($type);
        if ($items->isNotEmpty()) {
            $grouped[$type] = $items;
        }
    }

    return $grouped;
}
}
