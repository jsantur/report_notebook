<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Reportes - Seguridad Ciudadana</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
   <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#f0f4f8] min-h-screen flex items-center justify-center p-4">

    <script>
        function loginHandler() {
            return {
                showRecovery: false,
                showPassword: false,
                recoveryStep: 1,
                loading: false,
                recoveryQuestion: '',
                recoveryData: {
                    username: '',
                    answer: '',
                    new_password: '',
                    new_password_confirmation: ''
                },
                
                get hasMinLength() {
                    return this.recoveryData.new_password.length >= 8;
                },
                get hasUppercase() {
                    return /[A-Z]/.test(this.recoveryData.new_password);
                },
                get hasLowercase() {
                    return /[a-z]/.test(this.recoveryData.new_password);
                },
                get hasNumber() {
                    return /[0-9]/.test(this.recoveryData.new_password);
                },
                get hasSpecialChar() {
                    return /[!@#$%^&*(),.?":{}|<>]/.test(this.recoveryData.new_password);
                },
                get passwordsMatch() {
                    return this.recoveryData.new_password && 
                           this.recoveryData.new_password === this.recoveryData.new_password_confirmation;
                },
                get isAnswerFilled() {
                    return this.recoveryData.answer.trim() !== '';
                },

                async getQuestion() {
                    if (!this.recoveryData.username) return;
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route("password.recovery") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ username: this.recoveryData.username })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.recoveryQuestion = data.question;
                            this.recoveryStep = 2;
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    } finally {
                        this.loading = false;
                    }
                },

                async resetPassword() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route("password.reset") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                username: this.recoveryData.username,
                                answer: this.recoveryData.answer,
                                new_password: this.recoveryData.new_password,
                                new_password_confirmation: this.recoveryData.new_password_confirmation
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                                this.showRecovery = false;
                                this.recoveryStep = 1;
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
                    } finally {
                        this.loading = false;
                }
            }
        }
    }
</script>

    <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden max-w-4xl w-full border border-gray-100">
        <div class="flex flex-col md:flex-row">
            <!-- Columna Izquierda - Logo -->
            <div class="md:w-1/2 bg-[#eef5f9] flex flex-col items-center justify-center p-8 md:p-12 relative overflow-hidden">
                <div class="text-center z-10">
                    <!-- Logo Seguridad Ciudadana Talara -->
                    <img src="/img/logo_login.png" alt="Seguridad Ciudadana Talara" class="w-80 h-auto mx-auto object-contain drop-shadow-lg transition-transform duration-500 hover:scale-105">
                </div>
                <div class="mt-12 text-center z-10 max-w-xs">
                    <p class="text-gray-600 font-medium text-sm leading-tight">
                        Municipalidad Provincial de Talara - Gestión de Seguridad
                    </p>
                </div>
            </div>
            
            <!-- Columna Derecha - Formulario -->
            <div class="md:w-1/2 p-8 md:p-12 bg-white flex flex-col justify-between">
                <div>
                    <div class="mb-10">
                        <h1 class="text-xl font-light text-gray-500">
                            Bienvenido al
                        </h1>
                        <h2 class="text-4xl font-extrabold text-black leading-tight tracking-tight">
                            Sistema de Reportes
                        </h2>
                        <p class="text-sm font-semibold text-gray-400 mt-1 uppercase tracking-widest">
                            Central de Monitoreo
                        </p>
                    </div>
                    
                    <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="loginHandler()">
                        @csrf
                        
                        <!-- Campo Usuario -->
                        <div x-show="!showRecovery">
                            <label for="usuario" class="sr-only">Usuario</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input type="text" name="usuario" id="usuario" 
                                       class="block w-full pl-12 pr-4 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-gray-700"
                                       placeholder="Usuario" value="{{ old('usuario') }}" :required="!showRecovery" autofocus>
                            </div>
                        </div>
                        
                        <!-- Campo Contraseña -->
                        <div x-show="!showRecovery">
                            <label for="password" class="sr-only">Contraseña</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                       class="block w-full pl-12 pr-12 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-gray-700"
                                       placeholder="Contraseña" :required="!showRecovery">
                                
                                <!-- Toggle Password Visibility -->
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <button type="button" @click="showPassword = !showPassword" class="text-gray-400 hover:text-blue-500 focus:outline-none transition-colors">
                                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" name="remember" id="remember" 
                                           class="h-4 w-4 text-[#0051A1] focus:ring-[#0051A1] border-gray-300 rounded transition-all cursor-pointer">
                                    <label for="remember" class="ml-2 block text-xs text-gray-500 font-medium cursor-pointer select-none">Recordar</label>
                                </div>
                                <button type="button" @click="showRecovery = true" class="text-xs text-blue-500 font-medium hover:text-blue-700 transition-colors hover:underline">¿Olvidó su contraseña?</button>
                            </div>
                        </div>

                        <!-- Recovery Section (Optimized for new UI) -->
                        <div x-show="showRecovery" x-transition class="space-y-4 bg-blue-50 p-6 rounded-2xl border border-blue-100">
                            <h3 class="text-sm font-bold text-[#1a365d] flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                Recuperar Acceso
                            </h3>
                            <!-- Step 1: Username -->
                            <div x-show="recoveryStep === 1" class="space-y-4">
                                <input type="text" x-model="recoveryData.username" placeholder="Ingrese su usuario" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                                <button type="button" @click="getQuestion" :disabled="loading" class="w-full py-3 bg-[#1a365d] text-white rounded-xl font-bold text-sm hover:bg-[#2a4365] transition disabled:opacity-50">
                                    Siguiente
                                </button>
                            </div>
                            <!-- Step 2: Answer -->
            <div x-show="recoveryStep === 2" class="space-y-4">
                <p class="text-xs font-medium text-gray-600 px-1" x-text="recoveryQuestion"></p>
                <input type="text" x-model="recoveryData.answer" placeholder="Su respuesta..." class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                
                <input type="password" x-model="recoveryData.new_password" placeholder="Nueva contraseña" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                
                <!-- Password Requirements -->
                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 space-y-2">
                    <p class="text-[10px] font-black text-blue-700 uppercase tracking-widest mb-2">Requisitos de la contraseña:</p>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="hasMinLength ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                            <svg x-show="hasMinLength" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                        <span :class="hasMinLength ? 'text-green-700 font-medium' : 'text-gray-500'">Mínimo 8 caracteres</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="hasUppercase ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                            <svg x-show="hasUppercase" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                        <span :class="hasUppercase ? 'text-green-700 font-medium' : 'text-gray-500'">Al menos una letra mayúscula</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="hasLowercase ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                            <svg x-show="hasLowercase" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                        <span :class="hasLowercase ? 'text-green-700 font-medium' : 'text-gray-500'">Al menos una letra minúscula</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="hasNumber ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                            <svg x-show="hasNumber" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                        <span :class="hasNumber ? 'text-green-700 font-medium' : 'text-gray-500'">Al menos un número</span>
                    </div>
                    <div class="flex items-center space-x-2 text-xs">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="hasSpecialChar ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                            <svg x-show="hasSpecialChar" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </span>
                        <span :class="hasSpecialChar ? 'text-green-700 font-medium' : 'text-gray-500'">Al menos un carácter especial (!@#$%^&amp;*(),.?":{}|&lt;&gt;)</span>
                    </div>
                </div>
                
                <input type="password" x-model="recoveryData.new_password_confirmation" placeholder="Confirmar contraseña" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                
                <!-- Password Match -->
                <div class="flex items-center space-x-2 text-xs">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center transition-colors" :class="passwordsMatch ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400'">
                        <svg x-show="passwordsMatch" class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    </span>
                    <span :class="passwordsMatch ? 'text-green-700 font-medium' : 'text-gray-500'">Las contraseñas coinciden</span>
                </div>
                
                <button type="button" @click="resetPassword" :disabled="loading || !isAnswerFilled || !hasMinLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSpecialChar || !passwordsMatch" class="w-full py-3 bg-green-600 text-white rounded-xl font-bold text-sm hover:bg-green-700 transition disabled:opacity-50">
                    Restablecer Contraseña
                </button>
            </div>
                            <button type="button" @click="showRecovery = false; recoveryStep = 1" class="w-full text-center text-xs text-gray-500 hover:text-gray-800 font-medium transition-colors">
                                Volver al Login
                            </button>
                        </div>
                        
                        <!-- Botón Entrar -->
                        <button type="submit" x-show="!showRecovery"
                                class="w-full flex items-center justify-center gap-2 py-4 px-4 rounded-full
                                       text-base font-bold text-white bg-gradient-to-r from-[#0051A1] to-[#004182] 
                                       hover:shadow-lg hover:from-[#004182] hover:to-[#003162]
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0051A1]
                                       transition-all duration-300 shadow-md">
                            Iniciar sesión
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                    
                    @if(session('status'))
                        <div class="mt-6 p-4 bg-green-50 border border-green-100 rounded-xl">
                            <p class="text-sm text-green-700 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ session('status') }}
                            </p>
                        </div>
                    @endif

                    @if($errors->any())
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error de acceso',
                                    text: 'Usuario o contraseña incorrecta',
                                    confirmButtonColor: '#0051A1',
                                    confirmButtonText: 'Entendido'
                                });
                            });
                        </script>
                    @endif
                </div>

                <!-- Footer info -->
                <div class="mt-12 text-[10px] text-gray-400 font-medium leading-relaxed border-t border-gray-100 pt-6">
                    <p>
                        © 2026 - Sistema Integrado de Seguridad. Todos los derechos reservados. 
                        Para asistencia técnica, contacte a 
                        <a href="https://wa.me/51916582265?text=Hola%20Joseph,%20necesito%20asistencia%20técnica%20con%20el%20Sistema%20de%20Reportes" 
                           target="_blank" 
                           class="text-blue-500 hover:text-blue-700 transition-colors font-bold decoration-blue-500/30 hover:underline">
                            @jsantur
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
