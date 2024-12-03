<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

// Controlador para el restablecimiento de la contraseña a través de email o mensaje SMS (teléfono)
////use composer require twilio/sdk
class ForgotPasswordController extends Controller
{
    //Servicio de mandar correo electrónico con Token de Cambio de contraseña
    
    /*
    public function sendResetLinkEmail(Request $request){
        // Valida que el campo 'email' esté presente y sea una dirección de correo válida
        $request->validate(['email' => 'required|email']);
        // Intenta enviar el enlace de restablecimiento de contraseña al correo electrónico proporcionado
        $status = Password::sendResetLink(
            $request->only('email')
        );
        // Devuelve una respuesta JSON dependiendo del resultado del envío
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200) // Éxito: enlace enviado
            : response()->json(['message' => __($status)], 400); // Error: fallo al enviar el enlace
    }
    */

    //Servicio de mandar código al número de teléfono
    public function sendResetCodeToPhone(Request $request){
        // Valida que el campo 'phone' esté presente
        $request->validate(
            ['phone' => 'required'],
            ['country_code' => 'required']
        );
        // Genera un código de restablecimiento de 6 dígitos
        $code = rand(100000, 999999);
        // Almacena el código en la caché con una validez de 5 minutos
        Cache::put('reset_code_'.$request->country_code.$request->phone, $code, 300);
        // Configura las credenciales de Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $twilioFrom = env('TWILIO_FROM');
        // Crea una instancia del cliente de Twilio
        $twilio = new Client($sid, $token);
        try {
            // Envía el código por SMS utilizando Twilio
            $twilio->messages->create(
                $request->country_code.$request->phone, // Número de teléfono del destinatario
                [
                    'from' => $twilioFrom,
                    'body' => "Su código de restablecimiento de contraseña es: $code"
                ]
            );
            // Devuelve una respuesta JSON indicando que el código ha sido enviado
            return response()->json(['message' => 'El código ha sido enviado.'], 200);
        } catch (\Exception $e) {
            // Maneja errores y devuelve una respuesta JSON detallada con el mensaje del error
            return response()->json([
                'message' => 'Error al enviar el código.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //Servicio de mandar código al email
    public function sendResetCodeByEmail(Request $request) {
        // Valida que el campo 'email' esté presente y sea una dirección de correo válida
        $request->validate(['email' => 'required|email']);
        // Genera un código de restablecimiento de 6 dígitos
        $code = rand(100000, 999999);
        // Almacena el código en la caché con una validez de 5 minutos
        Cache::put('reset_code_'.$request->email, $code, 300);
        // Enviar correo con el código
        try {
            Mail::send('mails.reset_code', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Código de Restablecimiento de Contraseña');
            });
            
            return response()->json(['message' => 'El código ha sido enviado por correo electrónico.'], 200);
        } catch (\Exception $e) {
            // Maneja errores y devuelve una respuesta JSON en caso de fallo
            return response()->json(['message' => 'Error al enviar el código.'], 500);
        }
    }
}


