<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ResetPasswordController;
//Perfilamiento del usuario
use App\Http\Controllers\Api\SurveyController;
use App\Http\Controllers\Api\PreguntasOnboardingController;
use App\Http\Controllers\Api\PreguntasPerfilamientoController;
use App\Http\Controllers\Api\TestMedbyteController;
use App\Http\Controllers\Api\PayUController;
use App\Http\Controllers\Api\DiarioController;
use App\Http\Controllers\Api\RachaController;
use App\Http\Controllers\Api\PlanPersonalizadoController;

//PRUEBAS API - TOKENIZACIÓN PAYU
// Ruta para crear un token
Route::post('/payu/create-token', [PayUController::class, 'createToken']);
// Ruta para validar un token
Route::post('/payu/validate-token', [PayUController::class, 'validateToken']);
// Ruta para realizar un pago
Route::post('/payu/make-payment', [PayUController::class, 'makePayment']);
// Ruta para consultar el estado de una transacción
Route::get('/payu/transaction-status', [PayUController::class, 'getTransactionStatus']);
// Ruta para procesar un reembolso
Route::post('/payu/refund-payment', [PayUController::class, 'refundPayment']);
// Ruta para manejar notificaciones de webhook
Route::post('/payu/webhook', [PayUController::class, 'handleWebhook']);
Route::post('/payu/token/details', [PayUController::class, 'getTokenDetails']);

//Ingresa el código de verificación de la cuenta.
Route::post('/verify_phone_code', [AuthController::class, 'verifyPhoneCode']);

// Ruta protegida por middleware auth:sanctum
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Agrupación de rutas comunes para Onboarding y Perfilamiento
Route::middleware('auth:sanctum')->group(function () {
    
    //Metodos de confirmación de cuenta
    Route::get('/verificar_cuenta', [AuthController::class, 'checkAccountVerification']);
    Route::post('/send_verification_code', [AuthController::class, 'sendVerificationCodeToPhone']);

    // Rutas para servicios del usuario
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('plan_actual', [AuthController::class, 'plan_actual'])->name('plan_actual');
        Route::get('tokens', [AuthController::class, 'user_tokens'])->name('user_tokens');
    });

  // Rutas para el manejo de las misiones, submisiones del plan personalizado.
    Route::prefix('plan')->name('plan.')->group(function () {
        Route::get('/habilitar_misiones', [PlanPersonalizadoController::class, 'habilitar_misiones'])->name('habilitar_misiones'); // Habilita las misiones que se deben activar ese día
        Route::get('/progreso', [PlanPersonalizadoController::class, 'progreso_full'])->name('info_user'); // Información del usuario
        Route::get('/info', [PlanPersonalizadoController::class, 'info_user'])->name('info_user'); // Información del usuario
        Route::post('/store', [PlanPersonalizadoController::class, 'store'])->name('store'); // Crear el plan personalizado
        Route::put('/update/{id}', [PlanPersonalizadoController::class, 'update'])->name('update_plan'); // Añadir nuevas misiones al plan personalizado del usuario
        Route::get('/show', [PlanPersonalizadoController::class, 'show_plan'])->name('show_plan'); // Mostrar el plan personalizado completo
        Route::get('/mision/{mision_id}', [PlanPersonalizadoController::class, 'show_mision'])->name('show_mision'); // Mostrar detalle de una misión
        Route::get('/mision/{mision_id}/detalle', [PlanPersonalizadoController::class, 'show_mision_submisiones']); // Mostrar detalle de una misión
        Route::get('/misiones/semanales', [PlanPersonalizadoController::class, 'misiones_semanales']); // Mostrar misiones semanales
        Route::get('/misiones/mensuales', [PlanPersonalizadoController::class, 'misiones_mensuales']); // Mostrar misiones mensuales
        Route::get('/mision/{mision_id}/submisiones', [PlanPersonalizadoController::class, 'show_submisiones'])->name('show_submisiones'); // Mostrar submisiones de una misión
        Route::put('/submision/{id}', [PlanPersonalizadoController::class, 'edit_submision'])->name('edit_submision'); // Añadir progreso a la submisión
        Route::post('/generar', [PlanPersonalizadoController::class, 'generar_mision'])->name('generar_mision'); // Generar misión
        Route::get('/ruta_misiones', [PlanPersonalizadoController::class, 'ruta_misiones'])->name('ruta_misiones'); // Información del usuario
    });


    // Rutas para Encuesta Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::post('inicio', [PreguntasOnboardingController::class, 'inicio'])->name('inicio');
        Route::post('siguiente_pregunta', [PreguntasOnboardingController::class, 'siguiente_pregunta'])->name('siguiente_pregunta');
        Route::post('store', [PreguntasOnboardingController::class, 'store'])->name('store');
    });
    // Rutas para Encuesta Perfilamiento
    Route::prefix('perfilamiento')->name('perfilamiento.')->group(function () {
        Route::post('inicio', [PreguntasPerfilamientoController::class, 'inicio'])->name('inicio');
        Route::get('planes_individuales', [PreguntasPerfilamientoController::class, 'planes_individuales'])->name('planes_individuales');
        Route::post('siguiente_pregunta', [PreguntasPerfilamientoController::class, 'siguiente_pregunta'])->name('siguiente_pregunta');
        Route::post('store', [PreguntasPerfilamientoController::class, 'store'])->name('store');
    });
    // Rutas para Test de MEDBYTE
    Route::prefix('test')->name('test.')->group(function () {
        Route::post('preguntas', [TestMedbyteController::class, 'preguntas'])->name('preguntas');
        Route::post('store', [TestMedbyteController::class, 'store'])->name('store');
        Route::get('score_calculado', [TestMedbyteController::class, 'score_calculado'])->name('score_calculado');
    });

    //Rutas de rachas
    Route::prefix('racha')->name('racha.')->group(function () {
        Route::post('/actualizar', [RachaController::class, 'actualizar'])->name('actualizar');
        Route::get('/actual', [RachaController::class, 'mostrarRachaActual'])->name('actual');
    });

    Route::prefix('racha')->name('racha.')->group(function () {
        Route::post('/actualizar', [RachaController::class, 'actualizar'])->name('actualizar');
        Route::get('/actual', [RachaController::class, 'mostrarRachaActual'])->name('actual');
    });

    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/top', [AuthController::class, 'top_users'])->name('user.top_users'); //Listado top 20
        Route::post('/change-password', [AuthController::class, 'change_password'])->name('user.change_password'); //Cambiar contraseña
        Route::get('/mis_logros', [AuthController::class, 'mis_logros'])->name('user.mis_logros'); //Logros obtenidos del usuario
        Route::post('/desactivar_cuenta', [AuthController::class, 'desactivar_cuenta'])->name('user.desactivar_cuenta');
    });

    //Rutas para el servicio de la agenda-diario
    Route::prefix('diario')->name('diario.')->group(function () {
        //Mostrar nota
        Route::get('/nota/{id}', [DiarioController::class, 'show_nota'])->name('diario.show_nota');
        // Obtener el historial de notas de un usuario
        Route::get('/historial', [DiarioController::class, 'historial'])->name('diario.historial');
        // Obtener el calendario de notas de un usuario
        Route::post('/calendario', [DiarioController::class, 'calendario'])->name('diario.calendario');
        // Crear una nueva nota
        Route::post('/nota', [DiarioController::class, 'store_nota'])->name('diario.store_nota');
        // Actualizar una nota existente
        Route::post('/nota/{id}', [DiarioController::class, 'update_nota'])->name('diario.update_nota');
        // Eliminar una nota
        Route::delete('/nota/{id}', [DiarioController::class, 'delete_nota'])->name('diario.delete_nota');
        // Obtener notas por día
        Route::post('/dia', [DiarioController::class, 'getNotasPorDia'])->name('diario.getNotasPorDia');
        // Listar todas las emociones disponibles
        Route::get('/emociones', [DiarioController::class, 'emociones'])->name('diario.emociones');
    });
});


//Otras rutas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('user.logout'); //Cerrar Sesión
});

//Google - Manejo sesiones
Route::get('auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Ruta para enviar el enlace de restablecimiento de contraseña por correo electrónico
Route::post('password/email', [ForgotPasswordController::class, 'sendResetCodeByEmail'])->name('password.email');
// Ruta para enviar el código de restablecimiento por teléfono
Route::post('password/phone', [ForgotPasswordController::class, 'sendResetCodeToPhone']);
// Ruta para mostrar el formulario de restablecimiento de contraseña con el token
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Ruta para restablecer la contraseña
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');


//Rutas que no necesitan token de autenticación.
Route::post('user/register',[AuthController::class,'store']); 
Route::post('user/login',[AuthController::class,'login']);