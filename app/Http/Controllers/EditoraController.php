<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EditoraController extends Controller
{
    // Controller methods will be defined here

    public function index()
    {
        return view('editoras.index');
    }
}
