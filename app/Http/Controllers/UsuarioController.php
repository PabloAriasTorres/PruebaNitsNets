<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Http\Controllers\Recurso\ComprobarInfo;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

/**
* @OA\Info(
*             title="API CMS", 
*             version="1.0",
*             description="Todas las rutas de la API"
* )
*
* @OA\Server(url="http://127.0.0.1:8000")

* @OA\SecurityScheme(
*     securityScheme="bearerAuth",
*     type="http",
*     scheme="bearer",
*     bearerFormat="JWT",
*     description="Usa un token de acceso para autenticar las peticiones"
* )
*/
class UsuarioController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Iniciar sesión",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"email","contrasenya"},
     *             @OA\Property(property="email", type="string", example="pablo@gmail.com"),
     *             @OA\Property(property="contrasenya", type="string", example="1234")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Token generado"),
     *     @OA\Response(response=400, description="Contraseña incorrecta"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function login(Request $request)
    {
        $email = $request->input('email');
        $contrasenya = $request->input('contrasenya');

        $usuario = Usuario::where('email', $email)->first();

        if (!$usuario) {
            $datos = [
                'usuario' => 'No existe ningún usuario con ese correo',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        //HE CREADO UN USUARIO PREDETERMINADO PARA QUE SEA ADMIN CON ESTOS DATOS
        if ($email === 'admin@gmail.com' && $contrasenya === 'admin') {
            $adminToken = $usuario->createToken('admin', ['create', 'update', 'delete']);
            $datos = [
                'token admin' => $adminToken->plainTextToken,
                'estado' => 200
            ];
            return response()->json($datos);
        }

        //COMPARA LAS DOS CONTRASEÑAS HASHEADAS PARA COMPROBAR SI ES LA MISMA CONTRASEÑA
        //QUE HAY EN LA BASE DE DATOS 
        if (Hash::check($contrasenya, $usuario->contrasenya)) {
            $basicoToken = $usuario->createToken('basico',[]);
            $datos = [
                'token basico' => $basicoToken->plainTextToken,
                'estado' => 200
            ];
            return response()->json($datos);
        } else {
            $datos = [
                'usuario' => 'No coincide la contraseña con el correo proporcionado',
                'estado' => 400
            ];
            return response()->json($datos);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/registro",
     *     summary="Registrar un nuevo usuario",
     *     tags={"Usuarios"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nombre", "email", "contrasenya", "conf_email"},
     *             @OA\Property(property="nombre", type="string", example="Pablo"),
     *             @OA\Property(property="email", type="string", format="email", example="pablo@gmail.com"),
     *             @OA\Property(property="conf_email", type="string", format="email", example="pablo@gmail.com"),
     *             @OA\Property(property="contrasenya", type="string", example="1234")
     *         )
     *     ),
     *     @OA\Response(response=200,description="Usuario registrado con éxito"),
     *     @OA\Response(response=500,description="Usuario existente, pruebe otro correo")
     * )
     */
    public function registro(StoreUsuarioRequest $request)
    {
        if($request->input('email') != $request->input('conf_email')){
            $datos = [
                'usuario' => 'Los dos correos no coinciden',
                'estado' => 400
            ];
            return response()->json($datos);
        }

        //SE GUARDAN LOS DATOS DEL USUARIO Y SI HAN PASADO TODAS LAS CONDICIONES QUE HAY EN EL
        //STOREREQUEST PUES SE HASHEA LA CONTRASEÑA Y SE SUBE A LA BASE DE DATOS PARA QUE SEA
        //MÁS SEGURA
        $usuarioDatos = $request->all();
        $contrasenya = $request->input('contrasenya');
        $usuarioDatos['contrasenya'] = bcrypt($contrasenya);
        $usuario = Usuario::create($usuarioDatos);

        $datos = [
            'usuario' => $usuario,
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/usuarios",
     *     summary="Obtener una lista de usuarios",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200,description="Lista de usuarios obtenida con éxito"),
     *     @OA\Response(response=404,description="No se han encontrado usuarios")
     * )
     */
    public function index()
    {
        //
        $usuarios = Usuario::paginate();
    
        $datos = ComprobarInfo::existeInfo('usuarios',$usuarios,'No se han encontrado los usuarios',404,$usuarios);
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
     * Store a newly created resource in storage.
     */
    public function store(StoreUsuarioRequest $request)
    {
        // COMO HAY QUE HACER UN REGISTRO Y LOGIN NO CREO QUE HAGA FALTA HACER UN POST PARA USUARIO
        // YA QUE EL REGISTRO ES EXACTAMENTE LO MISMO
    }

    /**
     * @OA\Get(
     *     path="/api/v1/usuarios/{id}",
     *     summary="Obtener detalles de un usuario específico",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del usuario"
     *     ),
     *     @OA\Response(response=200,description="Detalles del usuario obtenidos con éxito"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function show(Usuario $usuario)
    {
        //
        $datos = ComprobarInfo::existeInfo('usuario',$usuario,'No se ha encontrado el usuario',404,$usuario);
        return response()->json($datos);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario)
    {
        //
    }

    /**
     * @OA\Put(
     *     path="/api/v1/usuarios/{id}",
     *     summary="Actualizar los datos de un usuario",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del usuario"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nombre", "email","contrasenya"},
     *             @OA\Property(property="nombre", type="string", example="Pablo"),
     *             @OA\Property(property="email", type="string", example="pablo@gmail.com"),
     *             @OA\Property(property="contrasenya", type="string", example="1234")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Usuario actualizado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function update(UpdateUsuarioRequest $request, Usuario $usuario)
    {
        //
        if(!$usuario){
            $datos = [
                'usuario' => 'No se ha encontrado el usuario',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $usuario->update($request->all());

        $datos = [
            'usuario' => 'El usuario ha sido actualizado correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/usuarios/{id}",
     *     summary="Eliminar un usuario",
     *     tags={"Usuarios"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID del usuario"
     *     ),
     *     @OA\Response(response=200, description="Usuario eliminado con éxito"),
     *     @OA\Response(response=403, description="No tiene permisos para realizar esta acción"),
     *     @OA\Response(response=404, description="Usuario no encontrado")
     * )
     */
    public function destroy(Usuario $usuario)
    {
        //
        $usuarioLogeado = Auth::user();

        if($usuarioLogeado == null || !$usuarioLogeado->tokenCan('delete')){
            $datos = [
                'usuario' => 'No tiene permisos para realizar esta acción',
                'estado' => 403
            ];
            return response()->json($datos);
        }

        if(!$usuario){
            $datos = [
                'usuario' => 'No se ha encontrado el usuario',
                'estado' => 404
            ];
            return response()->json($datos);
        }

        $usuario->delete();

        $datos = [
            'usuario' => 'El usuario ha sido eliminado correctamente',
            'estado' => 200
        ];
        return response()->json($datos);
    }
}
