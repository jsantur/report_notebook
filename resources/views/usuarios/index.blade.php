@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Seguridad Ciudadana')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
<div class="max-w-7xl mx-auto px-4" x-data="userRegistration()">
    <!-- Cabecera Premium (Estilo Kilometrajes) -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <!-- Izquierda: Identidad -->
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3.5 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Gestión de Usuarios</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Control de accesos y perfiles del sistema</p>
            </div>
        </div>

        <!-- Derecha: Acciones -->
        <div class="flex items-center space-x-4 w-full lg:w-auto">
            <button @click="resetForm()" class="w-full lg:w-auto px-10 py-4 bg-blue-600 text-white rounded-[24px] font-black text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                NUEVO USUARIO
            </button>
        </div>
    </div>

    <!-- Card Formulario de Registro/Edición -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12 transition-all duration-500" :class="editingId ? 'ring-2 ring-blue-500 shadow-xl' : ''">
        <div class="bg-gray-50/50 px-8 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 flex items-center">
                <span x-text="editingId ? 'EDITAR USUARIO EXISTENTE' : 'REGISTRAR NUEVO USUARIO'"></span>
            </h3>
            <template x-if="editingId">
                <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-2 py-1 rounded-full">MODO EDICIÓN ACTIVO</span>
            </template>
        </div>
        
        <form @submit.prevent="submitForm" class="p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Columna 1: Datos Personales -->
                <div class="space-y-6">
                    <div class="space-y-2" x-data="{ open: false, search: '' }">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Seleccionar Personal
                        </label>
                        <div class="relative">
                            <input type="text" 
                                x-model="formData.name" 
                                @click="open = true"
                                @input="open = true; search = $event.target.value"
                                placeholder="Buscar personal..." 
                                class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700" required>
                            
                            <div x-show="open" 
                                @click.away="open = false"
                                class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl max-h-64 overflow-y-auto custom-scrollbar">
                                @foreach($personnel as $person)
                                    @php 
                                        $fullName = $person->apellido_paterno . ' ' . $person->apellido_materno . ' ' . $person->nombres; 
                                        $dni = $person->dni;
                                    @endphp
                                    <div x-show="search === '' || 
                                          '{{ strtolower($fullName) }}'.includes(search.toLowerCase()) || 
                                          '{{ $dni }}'.includes(search)"
                                        @click="formData.name = '{{ $fullName }}'; open = false; search = ''"
                                        class="px-5 py-3 hover:bg-blue-50 cursor-pointer transition-colors border-b border-gray-50 last:border-0">
                                        <div class="font-bold text-gray-800 text-sm">{{ $fullName }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">DNI: {{ $person->dni }} • {{ $person->perfil_trabajo }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Rol de Acceso
                        </label>
                        <select x-model="formData.role" class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700 appearance-none bg-no-repeat bg-right" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-size: .65em auto; background-position: right 1rem top 50%;">
                            <option value="user">Usuario Estándar</option>
                            <option value="admin">Administrador del Sistema</option>
                        </select>
                    </div>
                </div>

                <!-- Columna 2: Credenciales -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                            Nombre de Usuario
                        </label>
                        <input type="text" x-model="formData.username" placeholder="ej: GSCxxxMPT" 
                            class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Contraseña
                            </span>
                            <template x-if="editingId">
                                <span class="text-[9px] text-orange-500 font-bold lowercase italic">Opcional si no desea cambiar</span>
                            </template>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="formData.password" :required="!editingId" placeholder="Mín. 8 caracteres" 
                                class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700">
                            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-3 text-gray-400 hover:text-blue-500 transition-colors">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </button>
                        </div>
                        
                        <!-- Password Requirements -->
                        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 space-y-2" x-show="formData.password || !editingId">
                            <p class="text-[10px] font-black text-blue-700 uppercase tracking-widest mb-2">Requisitos de la contraseña:</p>
                            <div class="flex items-center space-x-2 text-xs">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="(!editingId || formData.password) ? (hasMinLength ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') : 'bg-gray-100 text-gray-300'">
                                    <svg x-show="(!editingId || formData.password) && hasMinLength" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </span>
                                <span :class="(!editingId || formData.password) ? (hasMinLength ? 'text-green-700 font-medium' : 'text-gray-500') : 'text-gray-400'">Mínimo 8 caracteres</span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="(!editingId || formData.password) ? (hasUppercase ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') : 'bg-gray-100 text-gray-300'">
                                    <svg x-show="(!editingId || formData.password) && hasUppercase" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </span>
                                <span :class="(!editingId || formData.password) ? (hasUppercase ? 'text-green-700 font-medium' : 'text-gray-500') : 'text-gray-400'">Al menos una letra mayúscula</span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="(!editingId || formData.password) ? (hasLowercase ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') : 'bg-gray-100 text-gray-300'">
                                    <svg x-show="(!editingId || formData.password) && hasLowercase" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </span>
                                <span :class="(!editingId || formData.password) ? (hasLowercase ? 'text-green-700 font-medium' : 'text-gray-500') : 'text-gray-400'">Al menos una letra minúscula</span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="(!editingId || formData.password) ? (hasNumber ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') : 'bg-gray-100 text-gray-300'">
                                    <svg x-show="(!editingId || formData.password) && hasNumber" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </span>
                                <span :class="(!editingId || formData.password) ? (hasNumber ? 'text-green-700 font-medium' : 'text-gray-500') : 'text-gray-400'">Al menos un número</span>
                            </div>
                            <div class="flex items-center space-x-2 text-xs">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="(!editingId || formData.password) ? (hasSpecialChar ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400') : 'bg-gray-100 text-gray-300'">
                                    <svg x-show="(!editingId || formData.password) && hasSpecialChar" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </span>
                                <span :class="(!editingId || formData.password) ? (hasSpecialChar ? 'text-green-700 font-medium' : 'text-gray-500') : 'text-gray-400'">Al menos un carácter especial (!@#$%^&amp;*(),.?":{}|&lt;&gt;)</span>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="space-y-2" x-show="formData.password || !editingId">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Confirmar contraseña
                            </label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" x-model="formData.password_confirmation" :required="!editingId || formData.password" placeholder="Confirmar contraseña" 
                                    class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700">
                            </div>
                        </div>
                        
                        <!-- Password Match -->
                        <div class="flex items-center space-x-2 text-xs" x-show="formData.password || !editingId">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="passwordsMatch ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                                <svg x-show="passwordsMatch" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                            </span>
                            <span :class="passwordsMatch ? 'text-green-700 font-medium' : 'text-gray-500'">Las contraseñas coinciden</span>
                        </div>
                    </div>
                </div>

                <!-- Columna 3: Seguridad -->
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Pregunta de Seguridad
                        </label>
                        <select x-model="formData.security_question" class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700 appearance-none bg-no-repeat bg-right" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-size: .65em auto; background-position: right 1rem top 50%;">
                            <option value="">Seleccione una pregunta...</option>
                            <option value="¿Cuál es el nombre de tu primera mascota?">¿Cuál es el nombre de tu primera mascota?</option>
                            <option value="¿En qué ciudad naciste?">¿En qué ciudad naciste?</option>
                            <option value="¿Cuál es el nombre de tu escuela primaria?">¿Cuál es el nombre de tu escuela primaria?</option>
                            <option value="¿Cuál es tu color favorito?">¿Cuál es tu color favorito?</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                Respuesta de Seguridad
                            </span>
                            <template x-if="editingId">
                                <span class="text-[9px] text-orange-500 font-bold lowercase italic">Opcional si no desea cambiar</span>
                            </template>
                        </label>
                        <input type="text" x-model="formData.security_answer" :required="!editingId" placeholder="Su respuesta secreta..." 
                            class="w-full bg-gray-50 border-none px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none font-medium text-gray-700">
                    </div>
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end space-x-4 border-t border-gray-50 pt-8">
                <button type="button" @click="resetForm()" class="px-6 py-3 text-gray-400 hover:text-gray-600 font-bold transition-colors">
                    CANCELAR
                </button>
                <button type="submit" 
                    :disabled="isSubmitting || 
                              (!editingId && (!hasMinLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecialChar || !passwordsMatch)) || 
                              (editingId && formData.password && (!hasMinLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecialChar || !passwordsMatch))"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center disabled:opacity-50">
                    <template x-if="isSubmitting">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="editingId ? 'ACTUALIZAR USUARIO' : 'CREAR USUARIO DEL SISTEMA'"></span>
                </button>
            </div>
        </form>
    </div>

    <!-- Buscador y Título -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <h3 class="text-xl font-bold text-gray-800 flex items-center">
            Personal Registrado:
            <span class="ml-3 bg-blue-100 text-blue-600 text-xs font-black px-2.5 py-1 rounded-full uppercase">{{ count($users) }} TOTAL</span>
        </h3>
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" id="searchInput" placeholder="Buscar (Usuario, Nombre o Rol)" class="block w-full pl-10 pr-3 py-2 border border-transparent bg-gray-100 rounded-lg focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all text-sm">
        </div>
    </div>

    <!-- Tabla de Personal Registrado -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
        <div class="overflow-x-auto">
            <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr class="border-b border-gray-100">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Usuario</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Nombre Completo</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Rol</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Última Conexión</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Estado</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="userList" class="divide-y divide-gray-50">
                        @include('usuarios.partials.list')
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginación -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100" id="paginationLinks">
            {{ $users->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Lógica de Búsqueda AJAX en tiempo real
    const searchInput = document.getElementById('searchInput');
    const userList = document.getElementById('userList');
    const paginationLinks = document.getElementById('paginationLinks');
    let timeout = null;

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                const query = searchInput.value;
                
                // Ocultar paginación durante la búsqueda
                if (query.length > 0) {
                    paginationLinks.classList.add('hidden');
                } else {
                    paginationLinks.classList.remove('hidden');
                }

                fetch(`{{ route('usuarios.index') }}?search=${query}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    userList.innerHTML = html;
                })
                .catch(error => console.error('Error en la búsqueda:', error));
            }, 300); // Debounce de 300ms
        });
    }

    function userRegistration() {
        return {
            editingId: null,
            formData: {
                name: '',
                username: '',
                password: '',
                password_confirmation: '',
                role: 'user',
                security_question: '',
                security_answer: ''
            },
            showPassword: false,
            isSubmitting: false,
            
            get hasMinLength() {
                return this.formData.password.length >= 8;
            },
            get hasUppercase() {
                return /[A-Z]/.test(this.formData.password);
            },
            get hasLowercase() {
                return /[a-z]/.test(this.formData.password);
            },
            get hasNumber() {
                return /[0-9]/.test(this.formData.password);
            },
            get hasSpecialChar() {
                return /[!@#$%^&*(),.?":{}|<>]/.test(this.formData.password);
            },
            get passwordsMatch() {
                if (this.editingId && !this.formData.password) {
                    return true; // if not changing password, no need to match
                }
                return this.formData.password && 
                       this.formData.password === this.formData.password_confirmation;
            },
            
            resetForm() {
                this.editingId = null;
                this.formData = {
                    name: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    role: 'user',
                    security_question: '',
                    security_answer: ''
                };
                this.showPassword = false;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            async submitForm() {
                this.isSubmitting = true;
                try {
                    const url = this.editingId ? `/usuarios/${this.editingId}` : '{{ route("usuarios.store") }}';
                    const method = this.editingId ? 'PUT' : 'POST';
                    
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.triggerNotification(data.message, 'success');
                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        this.triggerNotification(data.message || 'Error al procesar', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.triggerNotification('No se pudo procesar la solicitud.', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            },

            async toggleActive(id, currentStatus) {
                const action = currentStatus ? 'suspender' : 'habilitar';
                const result = await Swal.fire({
                    title: `¿Desea ${action} este usuario?`,
                    text: `El usuario ${currentStatus ? 'ya no' : 'ahora'} podrá acceder al sistema.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: `Sí, ${action}`,
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: currentStatus ? '#f87171' : '#10b981'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/usuarios/${id}/toggle-active`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.triggerNotification(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } catch (error) {
                        this.triggerNotification('No se pudo cambiar el estado', 'error');
                    }
                }
            },

            editUser(user) {
                this.editingId = user.id;
                this.formData.name = user.name;
                this.formData.username = user.username;
                this.formData.role = user.role;
                this.formData.security_question = user.security_question;
                // Dejar password y security_answer vacíos para el backend
                this.formData.password = '';
                this.formData.security_answer = '';
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
                
                this.triggerNotification('Modo edición activado', 'info');
            },

            async deleteUser(id) {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/usuarios/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.triggerNotification(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } catch (error) {
                        this.triggerNotification('No se pudo eliminar el usuario', 'error');
                    }
                }
            },

            triggerNotification(msg, type = 'success') {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: type } }));
            }
        }
    }
</script>
@endpush
@endsection
