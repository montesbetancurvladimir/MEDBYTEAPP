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

    public function inicio(){
        // Obtener el mensaje de la sesión
        $mensaje = session('mensaje');
        // Eliminar el mensaje después de usarlo
        session()->forget('mensaje');
        // Pasar el mensaje a la vista
        return view('inicio', compact('mensaje'));
    }

}
