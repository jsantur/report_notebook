<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;
use App\Models\Serenazgo;

class UpdateSerenazgoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'dni' => $this->normalizeDigits($this->dni),
            'celular' => $this->normalizeDigits($this->celular),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $serenazgoId = $this->route('serenazgo')->id;
        
        return [
            'nombres' => 'required|string|max:255',
            'apellido_paterno' => 'required|string|max:255',
            'apellido_materno' => 'required|string|max:255',
            'dni' => ['required', 'digits:8', Rule::unique('serenazgos', 'dni')->ignore($serenazgoId)],
            'fecha_nacimiento' => 'required|date',
            'celular' => 'nullable|digits:9',
            'perfil_trabajo' => ['required', Rule::in([
                'Supervisor Encargado',
                'Chofer',
                'Operador de Cámaras',
                'Sereno',
                'Supervisor de Cámaras'
            ])],
            'nombre_foto' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombres.required' => 'Te falta ingresar Nombres.',
            'apellido_paterno.required' => 'Te falta ingresar Apellido Paterno.',
            'apellido_materno.required' => 'Te falta ingresar Apellido Materno.',
            'dni.required' => 'Te falta ingresar DNI.',
            'dni.digits' => 'El DNI debe tener 8 dígitos.',
            'dni.unique' => 'Ese DNI ya está registrado.',
            'fecha_nacimiento.required' => 'Te falta ingresar Fecha de Nacimiento.',
            'fecha_nacimiento.date' => 'La Fecha de Nacimiento no es válida.',
            'perfil_trabajo.required' => 'Te falta escoger Perfil de trabajo.',
            'perfil_trabajo.in' => 'Selecciona un Perfil de trabajo válido.',
            'celular.digits' => 'El celular debe tener 9 dígitos (o déjalo vacío).',
            'nombre_foto.max' => 'El nombre de la foto es demasiado largo.',
        ];
    }

    private function normalizeDigits(?string $value): ?string
    {
        if ($value === null) return null;
        $digits = preg_replace('/\D+/', '', $value);
        return $digits === '' ? null : $digits;
    }
}
