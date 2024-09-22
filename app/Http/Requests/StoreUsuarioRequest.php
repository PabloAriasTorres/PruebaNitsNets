<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'nombre' => ['required'],
            'email' => ['required','email'],
            'contrasenya' => ['required']
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
