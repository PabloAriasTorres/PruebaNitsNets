<?php

namespace App\Http\Controllers;

use App\Models\Pista;
use App\Models\Deporte;
use App\Models\Horario;
use App\Http\Requests\StorePistaRequest;
use App\Http\Requests\UpdatePistaRequest;
use Illuminate\Http\Request;
use DateTime;
use App\Http\Controllers\Recurso\ComprobarInfo;
use Illuminate\Support\Facades\Auth;

class PistaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/pistas",
     *     summary="Obtener una lista de pistas o horarios disponibles",
     *     tags={"Pistas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="fecha",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-09-21 10:00:00"),
     *         description="Fecha para consultar horarios disponibles"
     *     ),
     *     @OA\Parameter(
     *         name="deporte",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", example="Fútbol"),
     *         description="Nombre del deporte para filtrar pistas"
     *     ),
     *     @OA\Response(response=200, description="Lista de pistas o horarios obtenidos con éxito"),
     *     @OA\Response(response=404, description="No se han encontrado las pistas")
     * )
     */
    public function index(Request $request)
    {
        //
        $fecha = $request->query('fecha');
        $deporte = $request->query('deporte');

        //SI NO HAY NINGÚN PARÁMETRO EN LA QUERY O EL PARÁMETRO PAGE TIENE ALGO DE INFORMACIÓN,
        //SIGNIFICA QUE ESTÁ HACIENDO UN GET ALL SIN NADA EN ESPECÍFICO, SI NO, ES QUE HAY ALGÚN
        //PARÁMETRO Y AHORA FILTROS
        if(!$request->all() || $request->filled('page')){
            $pistas = Pista::paginate();
        
            $datos = ComprobarInfo::existeInfo('pistas',$pistas,'No se han encontrado las pistas',404,$pistas);
            return response()->json($datos);
        }

        $deporte_id = Deporte::where('nombre', $deporte)->value('id');
        $pistasIds = Pista::where('deporte_id', $deporte_id)->pluck('id');

        $fecha = new DateTime($fecha);
        $hora = $fecha->format('H:i:s');
        $fecha = $fecha->format('Y-m-d');

        $horariosDisponibles = Horario::whereIn('pista_id',$pistasIds)
        ->whereDate('fecha',$fecha)
        ->where('hora',$hora)
        ->where('reservada',false)
        ->get();

        if(count($horariosDisponibles) == 0){
            $datos = [
                'horarios' => 'No hay pistas disponibles para esa fecha',
                'estado' => 200
            ];
        }else{
            $datos = [
                'horarios' => $horariosDisponibles,
                'estado' => 200
            ];
        }
        return response()->json($datos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *     path="/api/v1/pistas",
     *     summary="Crear una nueva pista",
     *     tags={"Pistas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"deporte_id","longitud","ancho"},
     *             @OA\Property(property="deporte_id", type="integer", example=1),
     *             @OA\Property(property="longitud", type="float", example=30.5),
     *             @OA\Property(property="ancho", type="float", example=20)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pista creada con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Deporte no encontrado")
     * )
     */
    public function store(StorePistaRequest $request)
    {
        //
        $deporte_id = $request->input('deporte_id');

        if(!Deporte::find($deporte_id)){
            $datos = [
                'pista' => 'No se ha encontrado el deporte de la pista',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $pista = Pista::create($request->all());

        $datos = [
            'pista' => $pista,
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/pistas/{id}",
     *     summary="Obtener detalles de una pista específica",
     *     tags={"Pistas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la pista"
     *     ),
     *     @OA\Response(response=200, description="Detalles de la pista obtenidos con éxito"),
     *     @OA\Response(response=404, description="Pista no encontrada")
     * )
     */
    public function show(Pista $pista)
    {
        //
        $datos = ComprobarInfo::existeInfo('pista',$pista,'No se ha encontrado la pista',404,$pista);
        return response()->json($datos);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pista $pista)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/v1/pistas/{id}",
     *     summary="Actualizar los datos de una pista",
     *     tags={"Pistas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la pista"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"deporte_id","longitud","ancho"},
     *             @OA\Property(property="deporte_id", type="integer", example=1),
     *             @OA\Property(property="longitud", type="float", example=30.5),
     *             @OA\Property(property="ancho", type="float", example=20)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Pista actualizada con éxito"),
     *     @OA\Response(response=404, description="Pista no encontrada"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción")
     * )
     */
    public function update(UpdatePistaRequest $request, Pista $pista)
    {
        //
        if(!$pista){
            $datos = [
                'pista' => 'No se ha encontrado la pista',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $deporte_id = $request->input('deporte_id');

        if($deporte_id != null && !Deporte::find($deporte_id)){
            $datos = [
                'pista' => 'No se ha encontrado el deporte de la pista',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $pista->update($request->all());

        $datos = [
            'pista' => 'Pista actualizada correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/pistas/{id}",
     *     summary="Eliminar una pista",
     *     tags={"Pistas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la pista"
     *     ),
     *     @OA\Response(response=200, description="Pista eliminada con éxito"),
     *     @OA\Response(response=404, description="Pista no encontrada"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción")
     * )
     */
    public function destroy(Pista $pista)
    {
        //
        $usuario = Auth::user();

        if($usuario == null || !$usuario->tokenCan('delete')){
            $datos = [
                'pista' => 'No tiene permisos para realizar esta acción',
                'estado' => 403
            ];
            return response()->json($datos);
        }

        if(!$pista){
            $datos = [
                'pista' => 'No se ha encontrado la pista',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $pista->delete();

        $datos = [
            'pista' => 'La pista ha sido eliminada correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }
}
