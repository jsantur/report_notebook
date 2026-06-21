<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Solo administradores deberían poder registrar usuarios, pero el middleware se encargará de esto en las rutas.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/', // lowercase
                'regex:/[A-Z]/', // uppercase
                'regex:/[0-9]/', // number
                'regex:/[!@#$%^&*(),.?":{}|<>]/' // special character
            ],
            'role' => 'required|in:admin,user',
            'security_question' => 'required|string|max:255',
            'security_answer' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'username.unique' => 'El nombre de usuario ya está en uso.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe incluir al menos una letra minúscula, una mayúscula, un número y un carácter especial.',
            'role.in' => 'El rol seleccionado no es válido.',
            'security_question.required' => 'La pregunta de seguridad es obligatoria.',
            'security_answer.required' => 'La respuesta de seguridad es obligatoria.',
        ];
    }
}
