<?php 

namespace App\Services;

use App\Models\ListadoLogro;
use App\Models\User;

class LogroService
{
    public function verificarYOtorgarLogros(User $user, $accion, $detalle)
    {
        switch ($accion) {
            case 'mision_completada':
                $this->verificarLogrosPorMision($user, $detalle);
                break;

            case 'nota_escrita':
                $this->verificarLogroPrimerNota($user);
                break;

            case 'plan_completado':
                $this->verificarLogroPlan($user);
                break;
        }
    }

    protected function verificarLogrosPorMision(User $user, $misionId)
    {
        if ($this->esPrimeraMisionCompletada($user, $misionId)) {
            $this->otorgarLogro($user, 'Primer Misión Completada');
        }

        if ($this->completoMisionesEjercicio($user)) {
            $this->otorgarLogro($user, 'Todas las Misiones de Ejercicio Completadas');
        }
    }

    protected function verificarLogroPrimerNota(User $user)
    {
        if ($user->notas()->count() === 1) {
            $this->otorgarLogro($user, 'Primer Nota Escrita');
        }
    }

    protected function verificarLogroPlan(User $user)
    {
        if ($user->planes()->where('estado', 'completado')->count() === 1) {
            $this->otorgarLogro($user, 'Primer Plan Completado');
        }
    }

    protected function otorgarLogro(User $user, $nombreLogro)
    {
        $logro = ListadoLogro::where('nombre', $nombreLogro)->first();

        if ($logro && !$user->logros->contains($logro->id)) {
            $user->logros()->attach($logro->id);
        }
    }

    protected function esPrimeraMisionCompletada($user, $misionId)
    {
        // Lógica para determinar si es la primera misión completada.
        return $user->misionesCompletadas->isEmpty() && $misionId;
    }

    protected function completoMisionesEjercicio($user)
    {
        // Lógica para verificar si completó todas las misiones de ejercicio físico.
        return $user->misionesCompletadas()->where('tipo', 'ejercicio')->count() === 3; // X es el total de misiones de ejercicio
    }
}
