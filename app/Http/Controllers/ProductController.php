<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Addon;                 // ✅ Importación correcta
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
        $addons = Addon::where('is_active', true)->get(); // ✅ Usa Addon

        return view('products.index', compact('categories', 'products', 'addons'));
    }

    public function show(Product $product)
{
    // Obtener configuraciones asociadas a este producto (a través de la tabla pivote)
    $configs = $product->configurations()->where('is_active', true)->get();

    $messageConfig = null;
    $otherConfigs = [];

    foreach ($configs as $config) {
        if ($config->config_type === 'message') {
            $messageConfig = $config;
        } else {
            $otherConfigs[$config->config_type][] = $config;
        }
    }

    $configurations = [];
    foreach ($otherConfigs as $type => $items) {
        $configurations[$type] = collect($items);
    }

    $addons = Addon::where('is_active', true)->get();

    return view('products.show', compact('product', 'configurations', 'addons', 'messageConfig'));
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

    // 🔥 Método para el modal de edición del carrito
    public function getCustomizationsData(Product $product)
{
    $configs = $product->configurations()->where('is_active', true)->get();
    $messageConfig = null;
    $otherConfigs = [];

    foreach ($configs as $config) {
        if ($config->config_type === 'message') {
            $messageConfig = $config;
        } else {
            $otherConfigs[$config->config_type][] = $config;
        }
    }

    $configurations = [];
    foreach ($otherConfigs as $type => $items) {
        $configurations[$type] = collect($items)->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price_modifier' => (float) $item->price_modifier,
            ];
        });
    }

    $addons = Addon::where('is_active', true)->get()->map(function($addon) {
        return [
            'id' => $addon->id,
            'name' => $addon->name,
            'description' => $addon->description,
            'price' => (float) $addon->price,
        ];
    });

    return response()->json([
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'base_price' => (float) $product->base_price,
        ],
        'configurations' => $configurations,
        'addons' => $addons,
        'message_config' => $messageConfig ? [
            'id' => $messageConfig->id,
            'price_modifier' => (float) $messageConfig->price_modifier,
        ] : null,
    ]);
}
}
