<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Socio;
use App\Models\Pista;
use App\Models\Horario;
use App\Models\Deporte;
use App\Http\Requests\StoreReservaRequest;
use App\Http\Requests\UpdateReservaRequest;
use Illuminate\Http\Request;
use DateTime;
use Log;
use App\Http\Controllers\Recurso\ComprobarInfo;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/reservas",
     *     summary="Obtener una lista de reservas o reservas por fecha",
     *     tags={"Reservas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="fecha",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-10-21"),
     *         description="Fecha para consultar las reservas"
     *     ),
     *     @OA\Response(response=200, description="Lista de reservas obtenidas con éxito"),
     *     @OA\Response(response=404, description="No se han encontrado las reservas")
     * )
     */
    public function index(Request $request)
    {
        //
        $fecha = $request->query('fecha');
        
        //SI NO HAY NINGÚN PARÁMETRO EN LA QUERY O EL PARÁMETRO PAGE TIENE ALGO DE INFORMACIÓN,
        //SIGNIFICA QUE ESTÁ HACIENDO UN GET ALL SIN NADA EN ESPECÍFICO, SI NO, ES QUE HAY ALGÚN
        //PARÁMETRO Y AHORA FILTROS
        if(!$request->all() || $request->filled('page')){
            $reservas = Reserva::paginate();
        
            $datos = ComprobarInfo::existeInfo('reservas',$reservas,'No se han encontrado las reservas',404,$reservas);
            return response()->json($datos);
        }

        $fecha = new DateTime($fecha);

        $reservas = Reserva::whereDate('fecha',$fecha)
        ->with(['pista.deporte','socio'])
        ->get();

        if(count($reservas) == 0){
            $datos = [
                'reservas' => 'No hay ninguna reserva para esa fecha',
                'estado' => 200
            ];
        }else{
            $datos = [
                'reservas' => $reservas,
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
     *     path="/api/v1/reservas",
     *     summary="Crear una nueva reserva",
     *     tags={"Reservas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"pista_id", "socio_id", "fecha", "hora"},
     *             @OA\Property(property="pista_id", type="integer", example=1),
     *             @OA\Property(property="socio_id", type="integer", example=1),
     *             @OA\Property(property="fecha", type="string", format="date", example="2024-10-21"),
     *             @OA\Property(property="hora", type="string", format="time", example="10:00:00")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reserva creada con éxito"),
     *     @OA\Response(response=404, description="Socio o pista no encontrados"),
     *     @OA\Response(response=400, description="Error en la fecha o hora de la reserva"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción")
     * )
     */
    public function store(StoreReservaRequest $request)
    {
        //
        $socio_id = $request->input('socio_id');
        $pista_id = $request->input('pista_id');
        $fecha = $request->input('fecha');
        $hora = $request->input('hora');

        $datos = ComprobarInfo::comprobarDatosReserva($socio_id,$pista_id,$fecha,$hora);

        if($datos['reserva'] != ''){
            return response()->json($datos);
        }

        $reserva = Reserva::create($request->all());

        //UNA VEZ CREADA LA RESERVA SE TIENE QUE ACTUALIZAR EL HORARIO DE LA PISTA QUE COINCIDA
        //CON LA FECHA Y LA HORA DE LA RESERVA, PARA MARCAR QUE ESTÁ RESERVADA
        $fecha = new DateTime($fecha);
        $hora = new DateTime($hora);

        $fecha = $fecha->format('Y-m-d');
        $hora = $hora->format('H:i:s');

        $horario = Horario::where('pista_id',$pista_id)
        ->where('fecha',$fecha)
        ->where('hora',$hora)->first();

        $horario->reservada = true;
        $horario->save();

        $datos = [
            'reserva' => $reserva,
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/reservas/{id}",
     *     summary="Obtener detalles de una reserva específica",
     *     tags={"Reservas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la reserva"
     *     ),
     *     @OA\Response(response=200, description="Detalles de la reserva obtenidos con éxito"),
     *     @OA\Response(response=404, description="Reserva no encontrada")
     * )
     */
    public function show(Reserva $reserva)
    {
        //
        $datos = ComprobarInfo::existeInfo('reserva',$reserva,'No se ha encontrado la reserva',404,$reserva);
        return response()->json($datos);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/v1/reservas/{id}",
     *     summary="Actualizar los datos de una reserva",
     *     tags={"Reservas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la reserva"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="pista_id", type="integer", example=1),
     *             @OA\Property(property="socio_id", type="integer", example=1),
     *             @OA\Property(property="fecha", type="string", format="date", example="2024-10-21"),
     *             @OA\Property(property="hora", type="string", format="time", example="10:00:00")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Reserva actualizada con éxito"),
     *     @OA\Response(response=404, description="Reserva no encontrada"),
     *     @OA\Response(response=400, description="Error en la fecha o hora de la reserva"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción")
     * )
     */
    public function update(UpdateReservaRequest $request, Reserva $reserva)
    {
        //
        if(!$reserva){
            $datos = [
                'reserva' => 'No se ha encontrado la reserva',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $socio_id = $request->input('socio_id');
        $pista_id = $request->input('pista_id');
        $fecha = $request->input('fecha');
        $hora = $request->input('hora');

        $datos = ComprobarInfo::comprobarDatosReserva($socio_id,$pista_id,$fecha,$hora);

        if($datos['reserva'] != ''){
            return response()->json($datos);
        }

        $reserva->update($request->all());

        $datos = [
            'reserva' => 'Reserva actualizada correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/reservas/{id}",
     *     summary="Eliminar una reserva",
     *     tags={"Reservas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID de la reserva"
     *     ),
     *     @OA\Response(response=200, description="Reserva eliminada con éxito"),
     *     @OA\Response(response=404, description="Reserva no encontrada"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción")
     * )
     */
    public function destroy(Reserva $reserva)
    {
        //
        $usuario = Auth::user();

        if($usuario == null || !$usuario->tokenCan('delete')){
            $datos = [
                'reserva' => 'No tiene permisos para realizar esta acción',
                'estado' => 403
            ];
            return response()->json($datos);
        }

        if(!$reserva){
            $datos = [
                'reserva' => 'No se ha encontrado la reserva',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $reserva->delete();

        $datos = [
            'reserva' => 'La reserva ha sido eliminada correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }
}
