<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AutorController extends Controller
{
    // Controller methods will be defined here

    public function index()
    {
        return view('autores.index');
    }
}
