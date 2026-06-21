<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReporteRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha' => 'required|date',
            'hora' => 'required',
            'supervisor_campo_id' => 'required|exists:serenazgos,id',
            'supervisores_camaras' => 'required|array|min:1',
            'supervisores_camaras.*' => 'exists:serenazgos,id',
            'ocurrencias_relevo' => 'nullable|string',
            'operadores_camaras' => 'nullable|string', // JSON
            'personal_campo' => 'nullable|string', // JSON
            'reporte_personal_patrullando' => 'nullable|string',
            'visualizaciones_resaltantes' => 'nullable|string',
        ];
    }
}
