<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        return view('stok.index');
    }

    public function editMinStock($id)
    {
        return view('stok.edit-min');
    }

    public function updateMinStock(Request $request, $id)
    {
        return redirect()->route('stok.index');
    }

    public function dismissAlert($id)
    {
        return redirect()->route('stok.index');
    }
}
