<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputBarangController extends Controller
{
    public function create()
    {
        return view('barang.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('barang.create');
    }
}
