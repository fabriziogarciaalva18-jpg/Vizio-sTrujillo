<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductConfiguration;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomizationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || !auth()->user()->is_admin) {
                    abort(403, 'No tienes permisos de administrador');
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $query = ProductConfiguration::with('products');

        if ($request->filled('product_id')) {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        if ($request->filled('config_type')) {
            $query->where('config_type', $request->config_type);
        }

        $configurations = $query->orderBy('config_type')->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $types = [
            'size' => 'Tamaño',
            'layers' => 'Número de pisos',
            'flavor' => 'Sabor',
            'filling' => 'Relleno',
            'covering' => 'Cobertura',
            'shape' => 'Forma',
            'color' => 'Color',
            'toppings' => 'Toppings',
            'message' => 'Mensaje personalizado',
            'decoration' => 'Decoración adicional',
        ];

        return view('admin.customizations.index', compact('configurations', 'products', 'types'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $types = [
            'size' => 'Tamaño',
            'layers' => 'Número de pisos',
            'flavor' => 'Sabor',
            'filling' => 'Relleno',
            'covering' => 'Cobertura',
            'shape' => 'Forma',
            'color' => 'Color',
            'toppings' => 'Toppings',
            'message' => 'Mensaje personalizado',
            'decoration' => 'Decoración adicional',
        ];
        return view('admin.customizations.create', compact('products', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'config_type' => 'required|in:size,layers,flavor,filling,covering,shape,color,toppings,message,decoration',
            'name' => 'required|string|max:255',
            'price_modifier' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);
        $validated['price_modifier'] = $request->input('price_modifier', 0);

        // Crear la configuración
        $configuration = ProductConfiguration::create($validated);

        // Asociar productos
        $configuration->products()->attach($request->product_ids);

        return redirect()->route('admin.customizations.index')
            ->with('success', 'Personalización creada exitosamente.');
    }

    public function edit(ProductConfiguration $customization)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $types = [
            'size' => 'Tamaño',
            'layers' => 'Número de pisos',
            'flavor' => 'Sabor',
            'filling' => 'Relleno',
            'covering' => 'Cobertura',
            'shape' => 'Forma',
            'color' => 'Color',
            'toppings' => 'Toppings',
            'message' => 'Mensaje personalizado',
            'decoration' => 'Decoración adicional',
        ];
        return view('admin.customizations.edit', compact('customization', 'products', 'types'));
    }

    public function update(Request $request, ProductConfiguration $customization)
    {
        $validated = $request->validate([
            'config_type' => 'required|in:size,layers,flavor,filling,covering,shape,color,toppings,message,decoration',
            'name' => 'required|string|max:255',
            'price_modifier' => 'nullable|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $request->input('sort_order', 0);
        $validated['price_modifier'] = $request->input('price_modifier', 0);

        $customization->update($validated);

        // Sincronizar productos
        $customization->products()->sync($request->product_ids);

        return redirect()->route('admin.customizations.index')
            ->with('success', 'Personalización actualizada correctamente.');
    }

    public function destroy(ProductConfiguration $customization)
    {
        $customization->products()->detach();
        $customization->delete();
        return redirect()->route('admin.customizations.index')
            ->with('success', 'Personalización eliminada.');
    }

    public function toggleStatus(ProductConfiguration $customization)
    {
        $customization->update(['is_active' => !$customization->is_active]);
        return redirect()->back()->with('success', 'Estado actualizado.');
    }
}
