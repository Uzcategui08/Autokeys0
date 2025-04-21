<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PnominaRequest extends FormRequest
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
			'id_pnomina' => 'required',
			'id_tnomina' => 'required',
			'inicio' => 'required',
			'fin' => 'required',
        ];
    }
}
