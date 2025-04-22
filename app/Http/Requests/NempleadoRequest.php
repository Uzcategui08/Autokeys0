<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NempleadoRequest extends FormRequest
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
			'id_nempleado' => 'required',
			'id_pnomina' => 'required',
			'id_empleado' => 'required',
			'total_descuentos' => 'required',
			'total_abonos' => 'required',
			'total_prestamos' => 'required',
			'total_pagado' => 'required',
            'metodo_pago' => 'required',
        ];
    }
}
