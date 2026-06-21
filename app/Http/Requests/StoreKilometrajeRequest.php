<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKilometrajeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // The controller will handle authorization via getTargetUserId if needed
    }

    public function rules()
    {
        return [
            'reportes' => 'required|array',
            'reportes.*.id' => 'required|integer',
            'reportes.*.km' => 'required|numeric|min:0',
            'reportes.*.ap' => 'required|numeric|min:0',
            'reportes.*.po' => 'required|numeric|min:0|max:10',
            'reportes.*.turnos' => 'nullable|string',
            'reportes.*.jurisdiccion' => 'nullable|string',
            'reportes.*.is_draft' => 'boolean',
        ];
    }
}
