<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use App\Http\Requests\StoreDeporteRequest;
use App\Http\Requests\UpdateDeporteRequest;
use App\Http\Controllers\Recurso\ComprobarInfo;
use Illuminate\Support\Facades\Auth;

class DeporteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/deportes",
     *     summary="Obtener una lista de deportes",
     *     tags={"Deportes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200,description="Lista de deportes obtenida con éxito"),
     *     @OA\Response(response=404,description="No se han encontrado deportes")
     * )
     */
    public function index()
    {
        //
        $deportes = Deporte::paginate();

        $datos = ComprobarInfo::existeInfo('deportes',$deportes,'No se han encontrado los deportes',404,$deportes);
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
     *     path="/api/v1/deportes",
     *     summary="Registrar un nuevo deporte",
     *     tags={"Deportes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Baloncesto")
     *         )
     *     ),
     *     @OA\Response(response=200,description="Deporte registrado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=500,description="Deporte existente, pruebe otro deporte")
     * )
     */
    public function store(StoreDeporteRequest $request)
    {
        //
        $deporte = Deporte::create($request->all());

        $datos = [
            'deporte' => $deporte,
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/deportes/{id}",
     *     summary="Obtener detalles de un deporte específico",
     *     tags={"Deportes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del deporte"
     *     ),
     *     @OA\Response(response=200,description="Detalles del deporte obtenidos con éxito"),
     *     @OA\Response(response=404, description="Deporte no encontrado")
     * )
     */
    public function show(Deporte $deporte)
    {
        //
        $datos = ComprobarInfo::existeInfo('deporte',$deporte,'No se ha encontrado el deporte',404,$deporte);
        return response()->json($datos);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deporte $deporte)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/v1/deportes/{id}",
     *     summary="Actualizar los datos de un deporte",
     *     tags={"Deportes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del deporte"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nombre"},
     *             @OA\Property(property="nombre", type="string", example="Baloncesto")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Deporte actualizado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Deporte no encontrado")
     * )
     */
    public function update(UpdateDeporteRequest $request, Deporte $deporte)
    {
        //
        if(!$deporte){
            $datos = [
                'deporte' => 'No se ha encontrado el deporte',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $deporte->update($request->all());

        $datos = [
            'deporte' => 'Deporte actualizado correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/deportes/{id}",
     *     summary="Eliminar un deporte",
     *     tags={"Deportes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del deporte"
     *     ),
     *     @OA\Response(response=200, description="Deporte eliminado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Deporte no encontrado")
     * )
     */
    public function destroy(Deporte $deporte)
    {
        //
        $usuario = Auth::user();

        if($usuario == null || !$usuario->tokenCan('delete')){
            $datos = [
                'deporte' => 'No tiene permisos para realizar esta acción',
                'estado' => 403
            ];
            return response()->json($datos);
        }

        if(!$deporte){
            $datos = [
                'deporte' => 'No se ha encontrado el deporte',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $deporte->delete();

        $datos = [
            'deporte' => 'El deporte ha sido eliminado correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }
}
