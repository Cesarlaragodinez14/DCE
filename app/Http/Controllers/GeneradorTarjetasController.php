<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneradorTarjetasController extends Controller
{
    /**
     * Muestra la vista principal del Generador de Tarjetas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('generador_tarjetas.index');
    }
} 