<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDraftUnidadesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'unidades' => 'required|array',
            'unidades.*.unidad_id' => 'required|string',
            'unidades.*.subtipo' => 'nullable|string',
            'unidades.*.placa' => 'nullable|string',
            'unidades.*.conductor' => 'nullable|string',
            'unidades.*.sector' => 'nullable|string',
        ];
    }
}
