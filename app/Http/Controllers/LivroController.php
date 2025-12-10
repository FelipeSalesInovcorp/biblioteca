<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LivroController extends Controller
{
    // Controller methods will be defined here

    public function index()
    {
        return view('livros.index');
    }

}
