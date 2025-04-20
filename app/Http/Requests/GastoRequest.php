<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GastoRequest extends FormRequest
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
			'id_gastos' => 'required',
			'f_gastos' => 'required',
			'id_tecnico' => 'required',
			'descripcion' => 'required|string',
			'subcategoria' => 'required|string',
			'valor' => 'required',
			'estatus' => 'required',
        ];
    }
}
