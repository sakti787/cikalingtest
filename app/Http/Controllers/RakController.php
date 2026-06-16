<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Rack;
use Illuminate\Http\Request;

class RakController extends Controller
{
    /**
     * Display the digital rack map.
     */
    public function index()
    {
        $racks = Rack::with(['category', 'products' => function($q) {
            $q->where('is_active', true)
              ->select('product_id', 'product_name', 'stock', 'rack_id');
        }])
        ->withCount(['products as active_products_count' => function($q) {
            $q->where('is_active', true);
        }])
        ->orderBy('rack_code')
        ->get()
        ->map(fn($r) => [
            'id'         => $r->rack_id,
            'code'       => $r->rack_code,
            'category'   => $r->category->category_name,
            'category_id'=> $r->category_id,
            'capacity'   => $r->capacity,
            'count'      => $r->active_products_count,
            'products'   => $r->products->map(fn($p) => [
                'id'    => $p->product_id,
                'name'  => $p->product_name,
                'stock' => $p->stock,
            ])->toArray(),
        ]);
        
        $categories = Category::orderBy('category_name')->get();
        
        return view('rak.index', compact('racks', 'categories'));
    }
}
