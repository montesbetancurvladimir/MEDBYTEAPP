<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

// Controlador para el restablecimiento de la contraseña
class ResetPasswordController extends Controller
{
    /**
     * Muestra el formulario de restablecimiento de contraseña con el token y correo electrónico.
     */
    public function showResetForm(Request $request, $token = null){
        // Devuelve el token y el email  en formato JSON
        return response()->json([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
        * Restablece la contraseña del usuario.
        * Token - Email 
        * Code - Teléfono
    */
    /*
    public function reset(Request $request){
        // Valida los campos del formulario de restablecimiento de contraseña
        try {
            $request->validate([
                'token' => 'required_without:phone', // Token es requerido si no se proporciona el teléfono
                'email' => 'required_without:phone|email', // Email es requerido si no se proporciona el teléfono
                'phone' => 'required_without:email', // Teléfono es requerido si no se proporciona el email
                'password' => 'required|confirmed|min:8', // Contraseña requerida, debe ser confirmada y tener al menos 8 caracteres
                'code' => 'required_with:phone' // Código de restablecimiento es requerido si se proporciona el teléfono
            ]);
        } catch (ValidationException $e) {
            // Transformar los errores en un array simple
            $errors = [];
            foreach ($e->errors() as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $errors[] = $error;
                }
            }
            // Devuelve una respuesta JSON con los errores de validación en un array simple
            return response()->json([
                'validation_errors' => $errors,
            ], 422);
        }
    
        // Si el email está presente en la solicitud
        if ($request->filled('email')) {
            // Intenta restablecer la contraseña usando el email y el token
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    // Actualiza la contraseña del usuario
                    $user->forceFill(['password' => Hash::make($password),])->save();
                }
            );
            // Devuelve una respuesta JSON dependiendo del resultado del restablecimiento
            return $status === Password::PASSWORD_RESET
                ? response()->json(['message' => 'Password reset successfully.'], 200) // Éxito: contraseña restablecida
                : response()->json(['message' => __($status)], 400); // Error: fallo en el restablecimiento
        }
        // Si el teléfono está presente en la solicitud
        if ($request->filled('phone')) {
            // Obtiene el código de restablecimiento almacenado en la caché
            $code = Cache::get('reset_code_'.$request->phone);
            // Verifica si el código proporcionado coincide con el código almacenado
            if ($code !== $request->code) {
                return response()->json(['message' => 'Invalid reset code.'], 400); // Error: código inválido
            }
            // Busca al usuario por teléfono
            $user = User::where('phone', $request->phone)->first();
            // Verifica si el usuario existe
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404); // Error: usuario no encontrado
            }
            // Actualiza la contraseña del usuario
            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['message' => 'Password reset successfully.'], 200); // Éxito: contraseña restablecida
        }
        // Si no se proporciona ni email ni teléfono.
        return response()->json(['message' => 'Petición Inválida.'], 400); // Error: solicitud inválida
    }
    */

    public function reset(Request $request) {
        // Valida los campos del formulario de restablecimiento de contraseña
        try {
            $request->validate([
                'email' => 'required_without:phone|email', // Email es requerido si no se proporciona el teléfono
                'phone' => 'required_without:email', // Teléfono es requerido si no se proporciona el email
                'country_code' => 'required_without:email', // Indicativo es requerido si no se proporciona el email
                'password' => 'required|confirmed|min:8', // Contraseña requerida, debe ser confirmada y tener al menos 8 caracteres
                'code' => 'required_with:phone' // Código de restablecimiento es requerido si se proporciona el teléfono
            ]);
        } catch (ValidationException $e) {
            // Transformar los errores en un array simple
            $errors = [];
            foreach ($e->errors() as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $errors[] = $error;
                }
            }
            // Devuelve una respuesta JSON con los errores de validación en un array simple
            return response()->json([
                'validation_errors' => $errors,
            ], 422);
        }
    
        // Si el email está presente en la solicitud
        if ($request->filled('email')) {
            // Obtiene el código de restablecimiento almacenado en la caché para el email
            $code = Cache::get('reset_code_'.$request->email);
    
            // Verifica si el código proporcionado coincide con el código almacenado
            if ($code !== $request->code) {
                return response()->json(['message' => 'Invalid reset code.'], 400); // Error: código inválido
            }
    
            // Busca al usuario por email
            $user = User::where('email', $request->email)->first();
            // Verifica si el usuario existe
            if (!$user) {
                return response()->json(['message' => 'User not found.'], 404); // Error: usuario no encontrado
            }
            
            // Actualiza la contraseña del usuario
            $user->password = Hash::make($request->password);
            $user->save();
            
            // Devuelve una respuesta JSON dependiendo del resultado del restablecimiento
            return response()->json(['message' => 'Password reset successfully.'], 200); // Éxito: contraseña restablecida
        }
    
        // Si el teléfono está presente en la solicitud
        if ($request->filled('phone')) {
            // Obtiene el código de restablecimiento almacenado en la caché
            $code = Cache::get('reset_code_'.$request->country_code.$request->phone);
            
            // Verifica si el código proporcionado coincide con el código almacenado
            if ($code !== $request->code) {
                return response()->json(['message' => 'Invalid reset code.'], 400); // Error: código inválido
            }
    
            // Busca al usuario por teléfono
            $user = User::where('phone', $request->phone)
            ->where('country_code', $request->country_code)
            ->first();
            // Verifica si el usuario existe
            if (!$user) {
                return response()->json(['message' => 'Usuario no encontrado.'], 404); // Error: usuario no encontrado
            }
    
            // Actualiza la contraseña del usuario
            $user->password = Hash::make($request->password);
            $user->save();
            
            // Devuelve una respuesta JSON dependiendo del resultado del restablecimiento
            return response()->json(['message' => 'Contraseña restaurada correctamente.'], 200); // Éxito: contraseña restablecida
        }
    
        // Si no se proporciona ni email ni teléfono
        return response()->json(['message' => 'Petición Inválida.'], 400); // Error: solicitud inválida
    }
    
    
}
