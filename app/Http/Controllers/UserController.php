<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $users->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
        }
        
        $users = $users->orderBy('created_at', 'desc')->paginate(10);
        $personnel = \App\Models\Serenazgo::where('activo', true)
            ->whereIn('perfil_trabajo', ['Operador de Cámaras', 'Supervisor de Cámaras'])
            ->orderBy('apellido_paterno')
            ->get();
            
        if ($request->ajax()) {
            return view('usuarios.partials.list', compact('users'))->render();
        }
            
        return view('usuarios.index', compact('users', 'personnel'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'password' => $request->password,
                'role' => $request->role,
                'security_question' => $request->security_question,
                'security_answer' => $request->security_answer,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'role' => $request->role,
                'security_question' => $request->security_question,
            ];

            // Solo actualizar contraseña si se proporciona una nueva
            if ($request->filled('password')) {
                $data['password'] = $request->password;
            }

            // Solo actualizar respuesta de seguridad si se proporciona una nueva
            if ($request->filled('security_answer')) {
                $data['security_answer'] = $request->security_answer;
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'No se pudo eliminar el usuario.'
            ], 500);
        }
    }

    public function toggleActive(User $user)
    {
        try {
            $user->update(['activo' => !$user->activo]);
            return response()->json([
                'success' => true,
                'message' => $user->activo ? 'Usuario habilitado.' : 'Usuario suspendido.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de usuario: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado.'
            ], 500);
        }
    }

    public function recovery(Request $request)
    {
        $request->validate(['username' => 'required|string']);
        
        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
        }

        return response()->json([
            'success' => true,
            'question' => $user->security_question
        ]);
    }

    public function validateRecovery(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'answer' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/', // lowercase
                'regex:/[A-Z]/', // uppercase
                'regex:/[0-9]/', // number
                'regex:/[!@#$%^&*(),.?":{}|<>]/' // special character
            ]
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->answer, $user->security_answer)) {
            return response()->json(['success' => false, 'message' => 'Respuesta de seguridad incorrecta.'], 401);
        }

        $user->update(['password' => $request->new_password]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida correctamente.'
        ]);
    }
}
