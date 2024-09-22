<?php

namespace App\Http\Controllers;

use App\Models\Socio;
use App\Http\Requests\StoreSocioRequest;
use App\Http\Requests\UpdateSocioRequest;
use App\Http\Controllers\Recurso\ComprobarInfo;
use Illuminate\Support\Facades\Auth;

class SocioController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/socios",
     *     summary="Obtener una lista de socios",
     *     tags={"Socios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200,description="Lista de socios obtenida con éxito"),
     *     @OA\Response(response=404,description="No se han encontrado socios")
     * )
     */
    public function index()
    {
        //
        $socios = Socio::paginate();

        $datos = ComprobarInfo::existeInfo('socios',$socios,'No se han encontrado los socios',404,$socios);
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
     *     path="/api/v1/socios",
     *     summary="Registrar un nuevo socio",
     *     tags={"Socios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nombre", "dni"},
     *             @OA\Property(property="nombre", type="string", example="Carlos"),
     *             @OA\Property(property="dni", type="string", example="12345678P")
     *         )
     *     ),
     *     @OA\Response(response=200,description="Socio registrado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=500,description="Socio existente, pruebe otro dni")
     * )
     */
    public function store(StoreSocioRequest $request)
    {
        //
        $socio = Socio::create($request->all());

        $datos = [
            'socio' => $socio,
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/socios/{id}",
     *     summary="Obtener detalles de un socio específico",
     *     tags={"Socios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del socio"
     *     ),
     *     @OA\Response(response=200,description="Detalles del socio obtenidos con éxito"),
     *     @OA\Response(response=404, description="Socio no encontrado")
     * )
     */
    public function show(Socio $socio)
    {
        //
        $datos = ComprobarInfo::existeInfo('socio',$socio,'No se ha encontrado el socio',404,$socio);
        return response()->json($datos);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Socio $socio)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/v1/socios/{id}",
     *     summary="Actualizar los datos de un socio",
     *     tags={"Socios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del socio"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nombre", "dni"},
     *             @OA\Property(property="nombre", type="string", example="Carlos"),
     *             @OA\Property(property="dni", type="string", example="12345678P")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Socio actualizado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Socio no encontrado")
     * )
     */
    public function update(UpdateSocioRequest $request, Socio $socio)
    {
        //
        if(!$socio){
            $datos = [
                'socio' => 'No se ha encontrado el socio',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $socio->update($request->all());

        $datos = [
            'socio' => 'El socio ha sido actualizado correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/socios/{id}",
     *     summary="Eliminar un socio",
     *     tags={"Socios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del socio"
     *     ),
     *     @OA\Response(response=200, description="Socio eliminado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Socio no encontrado")
     * )
     */
    public function destroy(Socio $socio)
    {
        //
        $usuario = Auth::user();

        if($usuario == null || !$usuario->tokenCan('delete')){
            $datos = [
                'socio' => 'No tiene permisos para realizar esta acción',
                'estado' => 403
            ];
            return response()->json($datos);
        }

        if(!$socio){
            $datos = [
                'socio' => 'No se ha encontrado el socio',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $socio->delete();

        $datos = [
            'socio' => 'El socio ha sido eliminado correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }
}
