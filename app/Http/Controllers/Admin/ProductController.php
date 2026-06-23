<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    private function checkAdmin()
    {
        if (!auth()->check() || !auth()->user()->is_admin) {
            abort(403, 'No tienes permisos de administrador');
        }
    }

        public function index()
{
    $this->checkAdmin();
    $products = Product::with('category')->latest()->paginate(15);
    $categories = ProductCategory::where('is_active', true)->orderBy('sort_order')->get();
    return view('admin.products.index', compact('products', 'categories'));
}


    public function create()
    {
        $this->checkAdmin();
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'product_type' => 'required|in:simple,configurable,catering',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Booleanos
        $validated['has_sizes'] = $request->has('has_sizes');
        $validated['has_layers'] = $request->has('has_layers');
        $validated['has_flavors'] = $request->has('has_flavors');
        $validated['has_fillings'] = $request->has('has_fillings');
        $validated['has_coverings'] = $request->has('has_coverings');
        $validated['is_active'] = $request->has('is_active');

        // Slug único
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        // Imagen
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('products', $filename, 'public');
            $validated['image_url'] = $filename;
        }

        // EL PRECIO SE GUARDA TAL CUAL (en soles)
        // No se multiplica por 100
        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $this->checkAdmin();
        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->checkAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:product_categories,id',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'product_type' => 'required|in:simple,configurable,catering',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Booleanos
        $validated['has_sizes'] = $request->has('has_sizes');
        $validated['has_layers'] = $request->has('has_layers');
        $validated['has_flavors'] = $request->has('has_flavors');
        $validated['has_fillings'] = $request->has('has_fillings');
        $validated['has_coverings'] = $request->has('has_coverings');
        $validated['is_active'] = $request->has('is_active');

        // Slug si cambió el nombre
        if ($product->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }
            $product->slug = $slug;
        }

        // Imagen
        if ($request->hasFile('image_url')) {
            if ($product->image_url) {
                Storage::disk('public')->delete('products/' . $product->image_url);
            }
            $file = $request->file('image_url');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('products', $filename, 'public');
            $validated['image_url'] = $filename;
        }

        // EL PRECIO SE ACTUALIZA TAL CUAL (en soles)
        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Producto actualizado.');
    }

    public function destroy(Product $product)
    {
        $this->checkAdmin();
        if ($product->image_url) {
            Storage::disk('public')->delete('products/' . $product->image_url);
        }
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Producto eliminado.');
    }

    public function toggleStatus(Product $product)
    {
        $this->checkAdmin();
        $product->update(['is_active' => !$product->is_active]);
        return redirect()->back()->with('success', 'Estado actualizado.');
    }
}
