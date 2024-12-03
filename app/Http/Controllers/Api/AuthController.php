<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

//Validación mediante API 
use Illuminate\Support\Facades\Validator;

use App\Http\Requests\Usuario\StoreRequest;
use App\Http\Requests\Usuario\PutRequest;

use App\Models\User;
use App\Models\UserEmpresa;
use App\Models\TeamEmpresa;
use App\Models\TipoDocumento;
use App\Models\TeamUser;
use App\Models\Empresa;
use App\Models\Campana;
use App\Models\EncuestaAplicable;
use App\Models\SeguimientoDia;
use App\Models\SaludMentalCosto;

use App\Imports\CsvImportUsuarios;
use App\Models\ProfileUser;
use App\Models\UserPunto;
use Excel;


//Importación de trait
use App\Traits\FuncionesMails; //Mandar emails

//Libreria para el envío de Emails.
use App\Mail\Notification;
use App\Models\UserSuscripcion;
use App\Models\UserToken;
use Illuminate\Support\Facades\Mail;

//Clase recordar contraseña
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client;

//composer require socialiteproviders/google
//composer require laravel/socialite

class AuthController extends Controller
{
    use FuncionesMails;
    //Inicio de sesión correo - contraseña
    //Añadir lo del atributo enabled de las cuentas desactivadas.
    public function login(Request $request){
        $permisos_usuario = [];
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        //Si la autenticación es correcta genera el token.
        if(Auth::attempt($credentials)){
            $token = Auth::user()->createToken('myapptoken')->plainTextToken;
            //Consulta los datos del usuario con ese email.
            $Usuario = User::select('users.*')->where([['email', '=', $request->email]])->first();
            /*
            $Permisos = [];
            //Si el perfil no tiene permisos se le asigna el de inicio por defecto.
            if(count($Permisos) == 0){
                //ID INICIO ENCRIPTADO.
                $permisos_usuario[0] = 7;
            }else{
                //Arma un arreglo con los permisos que tenga.
                for ($i=0; $i < count($Permisos); $i++) { 
                    $permisos_usuario[$i] = $Permisos[$i]->profile_user_type_id;
                }
            }
            */
            
            //Añade los datos del usuario
            $collect[0] = array(
                "user_id"=>$Usuario->id,
                "token" => $token,
                "name" => $Usuario->name,
                "first_name" => $Usuario->first_name,
                "country_id" => $Usuario->country_id,
                "phone" => $Usuario->phone,
                "email" => $Usuario->email,
                //"permisos" => $permisos_usuario
            );
            return response()->json($collect);
        }else{
            return response()->json(['message' => 'Credenciales Incorrectas'], 401);
        }
    }

    //Registro - Manual
    public function store(Request $request){
        // Definir las reglas de validación
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required',
        ]);

        // Si la validación falla, retornar los errores
        if ($validator->fails()) {
            // Convertir los errores a un arreglo simple
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            return response()->json(['errors' => $errors], 422);
        }

        // Genera una contraseña aleatoria
        //$password = generatePassword(12);

        // Crea el usuario
        $Usuario_response = User::create([
            'name' => '-',
            'lastname' => '-',
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        //Crea el puntaje del usuario
        UserPunto::create([
            'user_id' => $Usuario_response->id,
            'total_puntos' => 0
        ]);

        // Verifica si la creación del usuario fue exitosa y retorna la respuesta apropiada
        if ($Usuario_response){
            $asunto = 'Bienvenido a Medbyte';
            $this->MailUsuarioCreado($request, $Usuario_response, $asunto); 
            //Retorna la respuesta en el servidor
            return response()->json(['message' => 'Usuario Creado', 'usuario' => $Usuario_response], 200);
        } else {
            return response()->json(['message' => 'Error'], 400);
        }
    }

    // Cierre de sesión
    public function logout(Request $request){
        // Revoke the token that was used to authenticate the current request
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }

    public function redirectToGoogle(){
        return Socialite::driver('google')->stateless()->redirect();
    }


    public function handleGoogleCallback(){
        $user = Socialite::driver('google')->stateless()->user();
        $existingUser = User::where('email', $user->getEmail())->first();
        if($existingUser){
            Auth::login($existingUser);
        } else {
            $newUser = User::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'google_id' => $user->getId(),
            ]);
            Auth::login($newUser);
        }
        return redirect('/home');
    }

    // Metodos de confirmación de cuenta.
    // Servicio de mandar código al número de teléfono
    public function sendVerificationCodeToPhone(Request $request){
        
        $user = auth()->id();
        // Valida que el campo 'phone' esté presente y que tenga el formato adecuado
        $request->validate([
            'phone' => 'required|min:10|max:15', // Ajusta los límites según tus necesidades
            'country_code' => 'required|string|min:1|max:5', // Validación del código del país
            'name' => 'required|string|max:255', // Validación del nombre
            'lastname' => 'required|string|max:255', // Validación del apellido
        ]);

        // Aquí se guarda el teléfono, nombre y código del país, después se manda el código de verificación.
        $phone = $request->phone;
        $country_code = $request->country_code;
        $name = $request->name;
        $lastname = $request->lastname;
        $full_phone = $request->country_code.$request->phone;

        // Encuentra al usuario por su ID (o cualquier otra forma que uses para identificarlo)
        $user = User::findOrFail($user);
        // Actualiza los campos en la tabla Users
        $user->phone = $phone;
        $user->country_code = $country_code;
        $user->name = $name;
        $user->lastname = $lastname;
        // Guarda los cambios
        $user->save();

        // Genera un código de verificación de 6 dígitos
        $code = rand(100000, 999999);
        // Almacena el código en la caché con una validez de 10 minutos
        Cache::put('verification_code_'.$full_phone, $code, 600);
        // Configura las credenciales de Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $twilioFrom = env('TWILIO_FROM');
        // Crea una instancia del cliente de Twilio
        $twilio = new Client($sid, $token);
        try{
            // Envía el código por SMS utilizando Twilio
            $twilio->messages->create(
                $full_phone, // Número de teléfono del destinatario
                [
                    'from' => $twilioFrom,
                    'body' => "Su código de verificación de cuenta es: $code"
                ]
            );
            
            // Devuelve una respuesta JSON indicando que el código ha sido enviado
            return response()->json(['message' => 'El código de verificación ha sido enviado.'], 200);
        } catch (\Exception $e) {
            // Maneja errores y devuelve una respuesta JSON en caso de fallo
            return response()->json(['message' => 'Error al enviar el código.'], 500);
        }
    }
    /*
    public function sendVerificationCodeToPhone(Request $request){
        
        // Valida que el campo 'phone' esté presente
        $request->validate(['phone' => 'required']);

        //Aquí se guarda el teléfono, nombre y código del país, después se manda el código de verificación.
        $phone = $request->phone;
        $country_code = $request->country_code;
        $name = $request->name;
        $lastname = $request->lastname;

        // Genera un código de verificación de 6 dígitos
        $code = rand(100000, 999999);

        dd($request->phone);
        // Almacena el código en la caché con una validez de 10 minutos
        Cache::put('verification_code_'.$request->phone, $code, 600);
        // Configura las credenciales de Twilio
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $twilioFrom = env('TWILIO_FROM');
        // Crea una instancia del cliente de Twilio
        $twilio = new Client($sid, $token);
        try{
            // Envía el código por SMS utilizando Twilio
            $twilio->messages->create(
                $request->phone, // Número de teléfono del destinatario
                [
                    'from' => $twilioFrom,
                    'body' => "Su código de verificación de cuenta es: $code"
                ]
            );
            
            // Devuelve una respuesta JSON indicando que el código ha sido enviado
            return response()->json(['message' => 'El código de verificación ha sido enviado.'], 200);
        } catch (\Exception $e) {
            // Maneja errores y devuelve una respuesta JSON en caso de fallo
            return response()->json(['message' => 'Error al enviar el código.'], 500);
        }
    }
    */

    // Servicio para verificar el código de verificación
    public function verifyPhoneCode(Request $request){
        // Valida que los campos 'phone' y 'code' estén presentes
        $request->validate([
            'phone' => 'required|exists:users,phone',
            'code' => 'required',
            'country_code' => 'required|string|min:1|max:5', // Validación del código del país
        ]);

        $phone = $request->phone;
        $country_code = $request->country_code;
        $full_phone = $request->country_code.$request->phone;

        // Recupera el código de la caché
        $cachedCode = Cache::get('verification_code_'.$full_phone);
        if ($cachedCode && $cachedCode == $request->code) {
            // Si el código es correcto, actualiza el campo 'confirmacion_cuenta' a true
            $user = User::where('phone', $phone)->where('country_code', $country_code)->first();
            $user->confirmacion_cuenta = true;
            $user->save();
            
            // Devuelve una respuesta JSON indicando que la cuenta ha sido verificada
            return response()->json(['message' => 'Cuenta verificada exitosamente.'], 200);
        } else {
            // Si el código es incorrecto, devuelve una respuesta de error
            return response()->json(['message' => 'Código de verificación incorrecto.'], 400);
        }
    }

    public function checkAccountVerification(){
        // Obtiene el ID del usuario autenticado
        $user_id = auth()->id();
        // Verifica si el ID del usuario es nulo
        if (is_null($user_id)) {
            return response()->json(['message' => 'Usuario no autenticado.', 'verified' => false], 401);
        }
        // Busca al usuario por su ID
        $user = User::find($user_id);
        // Verifica si se encontró el usuario
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.', 'verified' => false], 404);
        }
        // Verifica si la cuenta está confirmada
        if ($user->confirmacion_cuenta) {
            return response()->json(['message' => 'La cuenta está verificada.', 'verified' => true], 200);
        } else {
            return response()->json(['message' => 'La cuenta no está verificada.', 'verified' => false], 200);
        }
    }
    
    // Servicio para determinar qué plan tiene el usuario.
    public function plan_actual() {
        try {
            $user = auth()->id();
            // Buscar la suscripción más reciente del usuario
            $plan = UserSuscripcion::where('user_id', $user)
                ->orderBy('id', 'desc')  
                ->first();
            // Verificar si existe un plan para el usuario
            if (!$plan) {
                return response()->json(['message' => 'No se encontró ninguna suscripción para este usuario.'], 404);
            }
            // Devolver la respuesta con el plan actual
            return response()->json(['plan_actual' => $plan], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json(['error' => 'Ocurrió un error al buscar el plan actual.'], 500);
        }
    }

    //Cantidad de tokens que tiene actualmente el usuario
    public function user_tokens(){
        $user = auth()->id();
        $tokens = UserToken::where('user_id', $user)
        ->orderBy('id', 'desc')  
        ->first();
        return response()->json(['cantidad_tokens' => $tokens->tokens], 200);
    }

    //FALTA RUTA http://medbytehome.test/api/password/change-password
    public function change_password(Request $request){
        // Validar la entrada del formulario
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        // Obtener el usuario autenticado
        $user = User::user();
        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual es incorrecta.'
            ], 400);
        }
        // Cambiar la contraseña del usuario
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Contraseña cambiada exitosamente.'
        ], 200);
    }

    //Top de usuarios con puntaje
    public function top_users(){
        // Obtener el usuario autenticado
        $currentUser = Auth::user();
        
        // Obtener los puntos del usuario autenticado
        $currentUserPuntos = DB::table('users_puntos')
            ->where('user_id', $currentUser->id)
            ->value('total_puntos');
    
        if ($currentUserPuntos === null) {
            return response()->json([
                'message' => 'El usuario no tiene puntos registrados.'
            ], 400);
        }
    
        // Obtener los 10 usuarios con más puntos que el usuario actual
        $topUsers = DB::table('users_puntos')
            ->join('users', 'users.id', '=', 'users_puntos.user_id')
            ->where('users_puntos.total_puntos', '>', $currentUserPuntos)
            ->orderBy('users_puntos.total_puntos', 'desc')
            ->limit(10)
            ->select('users.id', 'users.name', 'users.lastname', 'users_puntos.total_puntos')  // Seleccionar campos necesarios
            ->get();
    
        // Obtener los 10 usuarios con menos puntos que el usuario actual
        $bottomUsers = DB::table('users_puntos')
            ->join('users', 'users.id', '=', 'users_puntos.user_id')
            ->where('users_puntos.total_puntos', '<', $currentUserPuntos)
            ->orderBy('users_puntos.total_puntos', 'desc')
            ->limit(10)
            ->select('users.id', 'users.name', 'users.lastname', 'users_puntos.total_puntos')  // Seleccionar campos necesarios
            ->get();
    
        return response()->json([
            'top_users' => $topUsers,
            'bottom_users' => $bottomUsers
        ], 200);
    }

    //Mis logros. (AÑADIR EL ICONO DEL LOGRO en el listado de logros)
    public function mis_logros(){
        // Obtener el usuario autenticado
        $currentUser = Auth::user()->id;
        // Obtener los logros del usuario
        $logros = DB::table('user_logros')
            ->join('listado_logros', 'listado_logros.id', '=', 'user_logros.logro_id')
            ->where('user_logros.user_id', $currentUser)
            ->select('user_logros.created_at', 'listado_logros.nombre', 'listado_logros.codigo', 'listado_logros.descripcion', 'listado_logros.icono')
            ->get();
        // Contar el total de logros conseguidos
        $contadorLogros = $logros->count();
        return response()->json([
            'total_logros' => $contadorLogros,
            'logros' => $logros
        ], 200);
    }

    //Cambiar el estado de la cuenta a deshabilitado
    public function desactivar_cuenta(Request $request){
        $user_id = auth()->id();
        $user = User::findOrFail($user_id);
        $user->enable = 0;
        $user->save();
        return response()->json(['message' => 'Cuenta inactivada correctamente.'], 200);
    }

}

//Funcion que genera contraseñas aleatorias.
function generatePassword($length){
    $bytes = random_bytes($length);
    return bin2hex($bytes);
}

