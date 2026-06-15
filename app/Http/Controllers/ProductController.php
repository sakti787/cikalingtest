<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with search and filters.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'rack']);
        
        if ($request->search) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $products = $query->orderBy('product_name')
            ->paginate(10)
            ->withQueryString();
            
        $categories = Category::orderBy('category_name')->get();
        
        return view('produk.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('produk.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('produk.index');
    }

    public function show($id)
    {
        return view('produk.show');
    }

    public function edit($id)
    {
        return view('produk.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('produk.index');
    }

    public function destroy($id)
    {
        return redirect()->route('produk.index');
    }

    public function deactivate($id)
    {
        return redirect()->route('produk.index');
    }

    public function search(Request $request)
    {
        return view('produk.search');
    }
}
