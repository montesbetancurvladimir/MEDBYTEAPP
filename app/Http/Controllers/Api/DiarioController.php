<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Nota;
use App\Models\Emocion;
use App\Models\ArchivoNota;
use Illuminate\Support\Facades\Storage;
//Validación mediante API
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

//Evento disparador de logros.
use App\Events\AccionRealizada;

/* SE DISPARA EL EVENTO CUANDO 
 if ($mision) {
            $user->misionesCompletadas()->attach($mision->id);
            event(new AccionRealizada($user, 'mision_completada', $mision->id));

            return response()->json(['message' => 'Misión completada'], 200);
        }
*/

class DiarioController extends Controller
{
    public function historial(){
        $user = auth()->id(); 
        // Obtener todas las notas del usuario, ordenadas por la fecha (más reciente a más antigua)
        $notas = Nota::where('notas.user_id', $user) // Filtrar por usuario
            ->join('emociones', 'notas.emocion', '=', 'emociones.id') // Hacer el INNER JOIN entre notas y emociones
            ->orderBy('notas.fecha_nota', 'desc') // Ordenar por fecha (más reciente a más antigua)
            ->select('notas.*', 'emociones.nombre as emocion_nombre') // Seleccionar las columnas de 'notas' y el nombre de la emoción
            ->get();
        // Retornar las notas encontradas, incluyendo el nombre de la emoción
        return response()->json([
            'notas' => $notas->map(function ($nota) {
                return [
                    'id' => $nota->id,
                    'titulo' => $nota->titulo,
                    'descripcion' => $nota->descripcion,
                    'emocion' => $nota->emocion_nombre, // Agregar el nombre de la emoción
                    'adjuntos' => $nota->adjuntos,
                    'privacidad' => $nota->privacidad,
                    'fecha_nota' => $nota->fecha_nota,
                ];
            })
        ], 200);
    }

    public function calendario(Request $request){
        $user = auth()->id();
        
        // Validar que el mes y año sean proporcionados y válidos
        $request->validate([
            'mes' => 'required|integer|between:1,12',
            'año' => 'required|integer',
        ]);

        $mes = $request->input('mes');
        $año = $request->input('año');

        // Obtener todas las notas del usuario para el mes especificado usando INNER JOIN con emociones
        $notas = Nota::where('notas.user_id', $user)
                    ->whereYear('notas.fecha_nota', $año)
                    ->whereMonth('notas.fecha_nota', $mes)
                    ->join('emociones', 'notas.emocion', '=', 'emociones.id') // INNER JOIN con la tabla emociones
                    ->orderBy('notas.fecha_nota', 'desc') // Ordenar por fecha para obtener la última nota del día
                    ->select('notas.*', 'emociones.nombre as emocion_nombre') // Seleccionar las columnas necesarias
                    ->get();

        // Crear un array con los días del mes
        $diasDelMes = [];
        $totalDias = cal_days_in_month(CAL_GREGORIAN, $mes, $año);

        for ($dia = 1; $dia <= $totalDias; $dia++) {
            // Filtrar las notas para el día actual
            $notasDelDia = $notas->filter(function ($nota) use ($dia, $mes, $año) {
                return $nota->fecha_nota->day == $dia && $nota->fecha_nota->month == $mes && $nota->fecha_nota->year == $año;
            });

            // Si hay notas para el día, obtenemos la emoción de la última nota (ya están ordenadas)
            if ($notasDelDia->isNotEmpty()) {
                $ultimaNotaDelDia = $notasDelDia->first();
                $emocion = $ultimaNotaDelDia->emocion_nombre ?? 'NA';
            } else {
                // Si no hay notas, emoción "neutral"
                $emocion = 'NA';
            }

            // Agregar la emoción al array del día
            $diasDelMes[] = [
                'dia' => $dia,
                'emocion' => $emocion,
            ];
        }

        // Retornar el array de días y emociones
        return response()->json([
            'dias' => $diasDelMes,
        ], 200);
    }

    public function store_nota(Request $request) {
        // Mensajes de validación personalizados
        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de caracteres.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'file' => 'El archivo debe ser de un tipo válido.',
            'mimes' => 'El archivo debe ser uno de los siguientes tipos: :values.',
            'max' => 'El archivo no puede exceder :max kilobytes.',
        ];
    
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'emocion' => 'required|string|max:255',
            //'adjuntos.*' => 'file|mimes:jpeg,png,pdf|max:2048', // Validación para archivos adjuntos
        ], $messages);
    
        // Verificar si falla la validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Crear la nota
        $user = auth()->id(); // Obtener el ID del usuario autenticado
        $nota = new Nota();
        $nota->user_id = $user;
        $nota->titulo = $request->input('titulo');
        $nota->descripcion = $request->input('descripcion');
        $nota->emocion = $request->input('emocion');
        $nota->privacidad = true;
        $nota->fecha_nota = now();
        // Guardar la nota en la base de datos
        $nota->save();
    
        // Manejar adjuntos si existen
        if ($request->hasFile('adjuntos')) {
            $adjuntos = [];
            foreach ($request->file('adjuntos') as $index => $file) {
                // Definir la ruta de la carpeta donde se almacenarán los archivos
                $folderPath = "notas/{$user}/{$nota->id}";
                // Generar un nombre único para el archivo
                $fileNameBasic = time() . "_" . uniqid() . "." . $file->extension();
                // Crear la ruta completa del archivo
                $filePath = $folderPath . "/" . $fileNameBasic;
                // Mover el archivo a la carpeta pública
                $file->move(public_path($folderPath), $fileNameBasic);
                // Añadir la ruta del archivo al array de adjuntos
                $adjuntos[] = $filePath;
                // Registro de la información en la tabla archivos (o similar)
                $data_archivo = array_merge($request->all(), [
                    'nota_id' => $nota->id,
                    'ruta_documento' => $fileNameBasic
                ]);
                // Suponiendo que tienes un modelo ArchivoNota para guardar la información en la base de datos
                ArchivoNota::create($data_archivo);
            }
            // Asignar los adjuntos a la nota y guardar la nota
            if (!empty($adjuntos)) {
                $nota->adjuntos = $adjuntos;
                $nota->save();
            }
        }
    
        // Retornar respuesta o redirigir
        return response()->json(['message' => 'Nota creada exitosamente', 'nota' => $nota], 201);
    }
    
    public function update_nota(Request $request, $id) {
        // Buscar la nota por su ID
        $nota = Nota::findOrFail($id);
    
        // Mensajes de validación personalizados
        $messages = [
            'string' => 'El campo :attribute debe ser una cadena de caracteres.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'file' => 'El archivo debe ser de un tipo válido.',
            'mimes' => 'El archivo debe ser uno de los siguientes tipos: :values.',
            'max' => 'El archivo no puede exceder :max kilobytes.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
        ];
    
        // Validar los datos entrantes
        $validator = Validator::make($request->all(), [
            'titulo' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string|max:1000',
            'emocion' => 'sometimes|string|max:255',
            'adjuntos' => 'nullable|array',
            'adjuntos.*' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'privacidad' => 'sometimes|boolean'
        ], $messages);

        // Verificar si falla la validación
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Obtener datos validados
        $validatedData = $validator->validated();
        // Actualizar los campos solo si están presentes en la solicitud
        $nota->titulo = $validatedData['titulo'] ?? $nota->titulo;
        $nota->descripcion = $validatedData['descripcion'] ?? $nota->descripcion;
        $nota->emocion = $validatedData['emocion'] ?? $nota->emocion;
        $nota->privacidad = $validatedData['privacidad'] ?? $nota->privacidad;
    
        // Lógica para manejar los adjuntos:
        if ($request->hasFile('adjuntos')) {
            // Eliminar todos los archivos antiguos asociados a la nota
            $oldAdjuntos = ArchivoNota::where('nota_id', $nota->id)->get();
            foreach ($oldAdjuntos as $archivo) {
                // Eliminar el archivo del sistema de archivos
                if (file_exists(public_path($archivo->ruta_documento))) {
                    unlink(public_path($archivo->ruta_documento));
                }
                // Eliminar el registro de la base de datos
                $archivo->delete();
            }
    
            // Añadir nuevos archivos
            $adjuntos = [];
            foreach ($request->file('adjuntos') as $file) {
                // Definir la ruta de la carpeta donde se almacenarán los archivos
                $folderPath = "notas/{$nota->user_id}/{$nota->id}";
                // Generar un nombre único para el archivo
                $fileNameBasic = time() . "_" . uniqid() . "." . $file->extension();
                // Crear la ruta completa del archivo
                $filePath = $folderPath . "/" . $fileNameBasic;
                // Mover el archivo a la carpeta pública
                $file->move(public_path($folderPath), $fileNameBasic);
                // Añadir la ruta del archivo al array de adjuntos
                $adjuntos[] = $filePath;
                // Guardar la información del archivo en la base de datos
                ArchivoNota::create([
                    'nota_id' => $nota->id,
                    'ruta_documento' => $filePath
                ]);
            }
    
            // Si se subieron archivos nuevos, actualizamos la propiedad de adjuntos de la nota
            if (!empty($adjuntos)) {
                $nota->adjuntos = $adjuntos;
            }
        } elseif ($request->has('eliminar_adjuntos')) {
            // Si no se subieron archivos nuevos pero se envió la solicitud para eliminar algunos adjuntos
            $eliminarAdjuntos = $request->input('eliminar_adjuntos');
            if (is_array($eliminarAdjuntos)) {
                foreach ($eliminarAdjuntos as $adjuntoId) {
                    // Buscar el archivo a eliminar
                    $archivo = ArchivoNota::find($adjuntoId);
                    if ($archivo && file_exists(public_path($archivo->ruta_documento))) {
                        // Eliminar el archivo del sistema de archivos
                        unlink(public_path($archivo->ruta_documento));
                        // Eliminar el registro de la base de datos
                        $archivo->delete();
                    }
                }
            }
        }
        // Guardar los cambios
        $nota->save();
        // Retornar la respuesta
        $nota = Nota::join('emociones', 'notas.emocion', '=', 'emociones.id')
                    ->where('notas.id', $id)
                    ->select('notas.*', 'emociones.id AS emocion_id', 'emociones.nombre as emocion_nombre')
                    ->first();

        $notaData = [
            'id' => $nota->id,
            'titulo' => $nota->titulo,
            'descripcion' => $nota->descripcion,
            'emocion_id' => $nota->emocion_id,
            'emocion' => $nota->emocion_nombre, // Agregar el nombre de la emoción
            'adjuntos' => $nota->adjuntos,
            'privacidad' => $nota->privacidad,
            'fecha_nota' => $nota->fecha_nota,
        ];

        return response()->json(['message' => 'Nota actualizada exitosamente', 'nota' => $notaData], 200);
    }

    public function delete_nota($id) {
        // Buscar la nota por su ID
        $nota = Nota::findOrFail($id);
    
        // Buscar y eliminar los archivos asociados a la nota
        $archivos = ArchivoNota::where('nota_id', $nota->id)->get();
        foreach ($archivos as $archivo) {
            // Eliminar el archivo del sistema de archivos
            if (file_exists(public_path($archivo->ruta_documento))) {
                unlink(public_path($archivo->ruta_documento));
            }
            // Eliminar el registro del archivo de la base de datos
            $archivo->delete();
        }
    
        // Ahora eliminar la nota
        $nota->delete();
    
        // Retornar una respuesta indicando que la eliminación fue exitosa
        return response()->json(['message' => 'Nota y archivos eliminados exitosamente.']);
    }    

    /*
    public function share_nota(){

    }
    */

    public function getNotasPorDia(Request $request){
        $user = auth()->id();
        // Validar que la fecha esté presente y sea una fecha válida
        $request->validate([
            'fecha_nota' => 'required|date',
        ]);

        // Capturar la fecha especificada
        $fechaNota = $request->input('fecha_nota');

        // Obtener todas las notas del usuario en esa fecha, ordenadas por la hora más temprana a la más tarde
        $notas = Nota::join('emociones', 'notas.emocion', '=', 'emociones.id') // INNER JOIN con la tabla 'emocions'
            ->where('notas.user_id', $user) // Filtrar por usuario
            ->whereDate('notas.fecha_nota', $fechaNota) // Filtrar por la fecha especificada
            ->orderBy('notas.fecha_nota', 'asc') // Ordenar por la hora (más temprano a más tarde)
            ->select('notas.*', 'emociones.nombre AS nombre_emocion', 'emociones.id AS emocion_id') // Seleccionar los campos de 'notas' y 'emocions'
            ->get();

        // Retornar las notas encontradas, incluyendo el nombre de la emoción
        return response()->json([
            'notas' => $notas->map(function ($nota) {
                return [
                    'id' => $nota->id,
                    'titulo' => $nota->titulo,
                    'descripcion' => implode(' ', array_slice(explode(' ', $nota->descripcion), 0, 13)),
                    'emocion_id' => $nota->emocion_id,
                    'emocion_nombre' => $nota->nombre_emocion, // Agregar el nombre de la emoción
                    'adjuntos' => $nota->adjuntos,
                    'privacidad' => $nota->privacidad,
                    'fecha_nota' => $nota->fecha_nota
                ];
            })
        ], 200);
    }

    //Listado de emociones disponibles
    public function emociones(){
        $emociones = Emocion::get();
        return response()->json(['emociones' => $emociones], 201);
    }

    public function show_nota($id){
        // Realizar el INNER JOIN entre la tabla 'notas' y 'emociones'
        $nota = Nota::join('emociones', 'notas.emocion', '=', 'emociones.id')
                    ->where('notas.id', $id)
                    ->select('notas.*', 'emociones.id AS emocion_id', 'emociones.nombre as emocion_nombre')
                    ->first();

        // Verificar si la nota existe
        if (!$nota) {
            return response()->json(['mensaje' => 'Nota no encontrada'], 404);
        }

        // Devolver la nota encontrada con el nombre de la emoción
        return response()->json([
            'id' => $nota->id,
            'titulo' => $nota->titulo,
            'descripcion' => $nota->descripcion,
            'emocion_id' => $nota->emocion_id,
            'emocion' => $nota->emocion_nombre, // Agregar el nombre de la emoción
            'adjuntos' => $nota->adjuntos,
            'privacidad' => $nota->privacidad,
            'fecha_nota' => $nota->fecha_nota,
        ]);
    }

}
