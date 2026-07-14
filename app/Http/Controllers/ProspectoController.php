<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProspectoController extends Controller
{
    public function index()
    {
        // Esto le dice a Laravel que busque en resources/views/prospectos/index.blade.php
        return view('prospectos.index');
    }
}
