<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductConfiguration;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        // Verificar que el usuario sea administrador
        $this->middleware(function ($request, $next) {
            if (!auth()->check() || !auth()->user()->is_admin) {
                abort(403, 'No tienes permisos de administrador');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'total_products' => Product::count(),
            'total_users' => User::where('is_admin', false)->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'delivering_orders' => Order::where('status', 'delivering')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'pending_review_payments' => Order::where('payment_status', 'pending_review')->count(),
            'recent_orders' => Order::with('user')->latest()->take(10)->get(),
            'inactive_products' => Product::where('is_active', false)->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function products()
    {
        // Redirigir al controlador específico de productos
        return redirect()->route('admin.products.index');
    }

    public function payments()
    {
        $pendingPayments = Order::where('payment_status', 'pending_review')
            ->with('user')
            ->latest()
            ->get();

        return view('admin.payments', compact('pendingPayments'));
    }
    public function categories()
{
    $categories = ProductCategory::orderBy('sort_order')->get();
    return view('admin.categories', compact('categories'));
}

public function customizations()
{
    $configurations = ProductConfiguration::with('product')->orderBy('product_id')->get();
    return view('admin.customizations', compact('configurations'));
}
}
