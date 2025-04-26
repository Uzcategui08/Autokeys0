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
			'tecnico' => 'required|string',
			'trabajo' => 'required|string',
			'cliente' => 'required|string',
			'telefono' => 'required|string',
			'valor_v' => 'required',
			'estatus' => 'required|string',
			'titular_c' => 'required|string',
			'cobro' => 'required|string',
			'descripcion_ce' => 'required|string',
			'monto_ce' => 'required',
			'porcentaje_c' => 'required|string',
			'marca' => 'required|string',
			'modelo' => 'required|string',
			'año' => 'required',
			'items' => 'required',
            'metodo_pce' => 'required|string',
        ];
    }
}
