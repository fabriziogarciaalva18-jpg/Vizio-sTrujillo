<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener los 4 productos más vendidos
        $topProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->where('products.is_active', true)
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(4)
            ->get();

        // Si no hay productos vendidos, obtener productos activos aleatorios
        if ($topProducts->isEmpty()) {
            $topProducts = Product::where('is_active', true)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return view('home', compact('topProducts'));
    }
}
