<?php

namespace App\Http\Controllers\Recurso;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use App\Models\Pista;
use App\Models\Socio;
use App\Models\Reserva;
use DateTime;

//ME HE CREADO ESTA CLASE PARA AHORRAR CÓDIGO Y NO TENER TANTO DUPLICADO
class ComprobarInfo
{
    public static function existeInfo($tabla,$objeto,$msjError,$etdError,$msjExito){
        if(!$objeto){
            $datos = [
                $tabla => $msjError,
                'estado' => $etdError
            ];
        }else{
            $datos = [
                $tabla => $msjExito,
                'estado' => 200
            ];
        }
        return $datos;
    }

    //SIMPLEMENTE SON COMPROBACIONES QUE SE HACEN TANTO EN EL POST COMO EN EL PUT DE RESERVAS
    //SIEMPRE DEVUELVO EL ARRAY DATOS Y LUEGO COMPRUEBO EN EL CONTROLADOR SI TIENE ALGO ALMACENADO
    //PARA SABER SI NO CUMPLE CON ALGUNA CONDICIÓN Y ENVIAR UN MENSAJE ESPECÍFICO AL USUARIO
    public static function comprobarDatosReserva($socio_id,$pista_id,$fecha,$hora){

        $datos = [
            'reserva' => ''
        ];

        if($socio_id != null && !Socio::find($socio_id)){
            $datos = [
                'reserva' => 'No se ha encontrado el socio de la reserva',
                'estado' => 404
            ];
        }

        if($pista_id != null && !Pista::find($pista_id)){
            $datos = [
                'reserva' => 'No se ha encontrado la pista de la reserva',
                'estado' => 404
            ];
        }

        $fechaTransformada = new DateTime($fecha);

        if($fecha != null && $fechaTransformada < new DateTime('2024-10-01') || $fechaTransformada > new DateTime('2024-10-31')){
            $datos = [
                'reserva' => 'No se puede hacer una reserva en esa fecha',
                'estado' => 400
            ];
        }

        $horaTransformada = new DateTime($hora);

        if($hora != null && ($horaTransformada < new DateTime('08:00:00') || $horaTransformada > new DateTime('22:00:00') ||
        $horaTransformada->format('i:s') != '00:00')){
            $datos = [
                'reserva' => 'Solamente se puede reservar de 08:00 a 22:00 en punto',
                'estado' => 400
            ];
        }

        $reserva = Reserva::where('pista_id',$pista_id)
        ->whereDate('fecha',$fecha)
        ->where('hora',$hora)
        ->first();

        if($reserva != null){
            $datos = [
                'reserva' => 'Ya hay una reserva para esa fecha',
                'estado' => 400
            ];
        }

        $reservasSocio = Reserva::where('socio_id',$socio_id)
        ->whereDate('fecha',$fecha)
        ->where('hora',$hora)
        ->get();

        if(count($reservasSocio) != 0){
            $datos = [
                'reserva' => 'No puedes tener dos reservas con la misma fecha',
                'estado' =>400
            ];
        }

        $cantidadReservasSocio = Reserva::where('socio_id',$socio_id)
        ->whereDate('fecha',$fecha)
        ->get();

        if(count($cantidadReservasSocio) == 3){
            $datos = [
                'reserva' => 'No puedes hacer más de 3 reservas el mismo día',
                'estado' => 400
            ];
        }

        return $datos;
    }
}