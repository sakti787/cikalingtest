<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RakController extends Controller
{
    public function index()
    {
        return view('rak.index');
    }
}
