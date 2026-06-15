<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('produk.index');
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
