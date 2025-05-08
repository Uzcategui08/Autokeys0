<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroVRequest extends FormRequest
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
			'fecha_h' => 'required',
			'id_empleado' => 'required|exists:empleados,id_empleado',
			'cliente' => 'required|string',
			'telefono' => 'required|string',
			'valor_v' => 'required',
			'estatus' => 'required|string',
			'titular_c' => 'required|string',
			'cobro' => 'nullable|string',
			'descripcion_ce' => 'nullable|string',
			'monto_ce' => 'nullable',
			'porcentaje_c' => 'required|string',
            'lugarventa' => 'required|string',
			'marca' => 'required|string',
			'modelo' => 'string',
			'año' => 'integer',
			'items' => 'required',
            'metodo_pce' => 'nullable|string',
            'tipo_venta' => 'required|string'
        ];
    }
}
