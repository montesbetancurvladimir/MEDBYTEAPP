<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function nosotros(){
        return view('nosotros');
    }

    public function empresas(){
        return view('empresas');
    }

    public function blog(){
        return view('blog');
    }

    public function prensa(){
        return view('prensa');
    }

}
