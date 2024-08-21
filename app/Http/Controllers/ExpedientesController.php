<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpedientesController extends Controller
{
    public function show()
    {
        return view('dashboard.expedientes.entrega');
    }
}
