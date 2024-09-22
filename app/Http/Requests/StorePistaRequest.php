<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class StorePistaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $usuario = Auth::user();
        return $usuario != null && $usuario->tokenCan('create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'deporte_id' => ['required','integer'],
            'longitud' => ['required','numeric'],
            'ancho' => ['required','numeric']
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $datos = [
            'errores' => $validator->errors(),
            'estado' => 400
        ];

        throw new HttpResponseException(response()->json($datos));
    }
}
