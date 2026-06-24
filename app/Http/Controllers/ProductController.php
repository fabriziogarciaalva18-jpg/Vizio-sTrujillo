<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Addon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::where('is_active', true)->orderBy('sort_order')->get();

        $query = Product::where('is_active', true)->with('category');

        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->get();
        $addons = Addon::where('is_active', true)->get();

        return view('products.index', compact('categories', 'products', 'addons'));
    }

public function show(Product $product)
{
    // Agrupar configuraciones por tipo
    $configurations = [
        'size' => $product->configurations()->where('config_type', 'size')->orderBy('sort_order')->get(),
        'layers' => $product->configurations()->where('config_type', 'layers')->orderBy('sort_order')->get(),
        'flavor' => $product->configurations()->where('config_type', 'flavor')->orderBy('sort_order')->get(),
        'filling' => $product->configurations()->where('config_type', 'filling')->orderBy('sort_order')->get(),
        'covering' => $product->configurations()->where('config_type', 'covering')->orderBy('sort_order')->get(),
        'shape' => $product->configurations()->where('config_type', 'shape')->orderBy('sort_order')->get(),
        'color' => $product->configurations()->where('config_type', 'color')->orderBy('sort_order')->get(),
        'toppings' => $product->configurations()->where('config_type', 'toppings')->orderBy('sort_order')->get(),
        'decoration' => $product->configurations()->where('config_type', 'decoration')->orderBy('sort_order')->get(),
    ];

    // Filtrar tipos vacíos
    $configurations = array_filter($configurations, function($items) {
        return $items->isNotEmpty();
    });

    $addons = Addon::where('is_active', true)->get();

    return view('products.show', compact('product', 'configurations', 'addons'));
}

    public function filter(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->get();

        return response()->json($products);
    }

    public function calculatePrice(Request $request, Product $product)
    {
        $selectedConfigs = $request->input('configurations', []);
        $quantity = $request->input('quantity', 1);
        $selectedAddons = $request->input('addons', []);

        $basePrice = (float) $product->base_price;

        foreach ($selectedConfigs as $configId) {
            $config = $product->configurations()->find($configId);
            if ($config) {
                $basePrice += (float) $config->price_modifier;
            }
        }

        $addonsPrice = 0;
        foreach ($selectedAddons as $addonId) {
            $addon = Addon::find($addonId);
            if ($addon) {
                $addonsPrice += (float) $addon->price;
            }
        }

        $total = ($basePrice + $addonsPrice) * $quantity;

        return response()->json([
            'base_price' => $basePrice,
            'addons_price' => $addonsPrice,
            'quantity' => $quantity,
            'total' => $total,
            'formatted_total' => 'S/. ' . number_format($total, 2)
        ]);
    }
}
