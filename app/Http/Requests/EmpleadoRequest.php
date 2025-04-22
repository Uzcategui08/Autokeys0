<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmpleadoRequest extends FormRequest
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
            'id_tnomina' => 'required|exists:tnominas,id_tnomina',
			'id_empleado' => 'required',
			'nombre' => 'required|string',
			'cedula' => 'required',
			'cargo' => 'required|string',
			'salario_base' => 'required',
			'metodo_pago' => 'required',
        ];
    }
}
