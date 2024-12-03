<?php

namespace  App\Traits;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

//Manejo de fechas.
use DateTime;
use Carbon\Carbon;

//Modelos
use App\Models\UserNotificacion; 
use App\Models\Contrato;

//Mandar correos.
use App\Mail\ExampleMail; //Clase mandar correo de ejemplo.
use App\Mail\RegisterUserMail;
use Illuminate\Support\Facades\Mail; 

trait FuncionesMails {

    //Al crearse un contrato en el metodo store, se manda un correo con la información básica.
    public function MailUsuarioCreado(Request $request, $usuario, $asunto){
        $subject = 'Bienvenido a Medbyte';
        $email = new RegisterUserMail($usuario,$asunto,$subject);
        // Enviar el correo electrónico a cada usuario.
        Mail::to($usuario->email)->send($email);
    }
}
