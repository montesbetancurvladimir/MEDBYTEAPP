<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Racha;
use Carbon\Carbon;

class RachaController extends Controller
{

    public function actualizar(Request $request){
        $userId = $request->user()->id;
        $this->actualizarRacha($userId);
        return response()->json(['mensaje' => 'Racha actualizada correctamente.'], 200);
    }

    public function obtenerRachaActual($userId){
        $racha = Racha::where('user_id', $userId)->first();
        if ($racha) {
            return $racha->racha_actual;
        }
        return 0; // O un valor predeterminado si no hay racha
    }
}

function actualizarRacha($userId){
    $racha = Racha::where('user_id', $userId)->first();

    $hoy = Carbon::today();
    $ayer = Carbon::yesterday();

    if ($racha) {
        if ($racha->ultima_fecha_accion->isSameDay($hoy)) {
            // La acción ya fue registrada hoy
            return $racha->racha_actual;
        } elseif ($racha->ultima_fecha_accion->isSameDay($ayer)) {
            // Incrementar la racha si la última acción fue ayer
            $racha->racha_actual++;
        } else {
            // Reiniciar la racha si ha pasado más de un día
            $racha->racha_actual = 1;
        }
        $racha->ultima_fecha_accion = $hoy;
        $racha->save();

    } else {
        // Si no existe, crear una nueva racha
        $racha = Racha::create([
            'user_id' => $userId,
            'racha_actual' => 1,
            'ultima_fecha_accion' => $hoy,
        ]);
    }

    return $racha->racha_actual;
}

