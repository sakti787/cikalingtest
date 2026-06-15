<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function exportPdf()
    {
        return response('PDF Export Stub', 200, ['Content-Type' => 'application/pdf']);
    }
}
