<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKilometrajeDraftRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'unidades' => 'required|array',
            'unidades.*.id' => 'required|integer',
            'unidades.*.km' => 'nullable|numeric|min:0',
            'unidades.*.ap' => 'nullable|integer|min:0',
            'unidades.*.po' => 'nullable|integer|min:0|max:10',
            'unidades.*.turnos' => 'nullable|string',
            'unidades.*.jurisdiccion' => 'nullable|string',
        ];
    }
}
