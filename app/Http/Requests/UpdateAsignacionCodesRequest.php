<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAsignacionCodesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled inside the controller via the Policy, 
        // but we could technically move it here. For now, it stays true.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'asignaciones' => 'required|array',
            'asignaciones.*.id' => 'required|exists:asignaciones,id',
            'asignaciones.*.cod_po' => 'nullable|string',
            'reporte_id' => 'nullable|exists:reportes,id',
            'distribucion_personal_campo' => 'nullable|string',
        ];
    }
}
