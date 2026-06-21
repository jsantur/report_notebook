@props(['turno'])

<script>
    window.reportHeaderControls = (turnoInicial) => ({
        turnoActual: turnoInicial,
        puedeGuardar: false,
        tiempoRestanteMs: 0,
        enVentanaCierre: false,
        mostrarPassword: false,
        verPassword: false,
        password: '',
        intentosFallidos: 0,
        passwordBloqueada: false,
        passwordCorrecta: 'password&clave&contrasena',
        serverTimeOffset: 0, // Offset between server time and client time (ms)

        init() {
            // Fetch server time initially and then every 5 minutes
            this.fetchServerTime();
            setInterval(() => this.fetchServerTime(), 300000); // 5 minutes
            // Update time every second
            setInterval(() => this.calcularTiempoRestante(), 1000);
            
            const savedIntentos = localStorage.getItem('report_intentos_fallidos');
            const savedBloqueo = localStorage.getItem('report_password_bloqueada');
            if (savedIntentos) this.intentosFallidos = parseInt(savedIntentos);
            if (savedBloqueo === 'true') this.passwordBloqueada = true;
            if (savedBloqueo === 'unlocked') this.passwordBloqueada = 'unlocked';
            
            // Disparar evento inicial basado en el estado guardado
            if (savedBloqueo === 'unlocked') {
                window.dispatchEvent(new CustomEvent('unlock-status-changed', { detail: { unlocked: true } }));
            }
        },

        async fetchServerTime() {
            try {
                const response = await fetch('{{ route('api.server-time') }}');
                const data = await response.json();
                const serverTime = new Date(data.datetime).getTime();
                const clientTime = Date.now();
                this.serverTimeOffset = serverTime - clientTime;
                // Calculate time immediately after fetching
                this.calcularTiempoRestante();
            } catch (e) {
                console.error('Error fetching server time:', e);
                // Fallback to client time if server time fails
                this.serverTimeOffset = 0;
            }
        },

        submitForm() {
            // Llamamos a clearDraft() desde el componente principal (reporte-main)
            const mainEl = document.getElementById('reporte-main');
            if (mainEl) {
                const mainData = Alpine.$data(mainEl);
                mainData.clearDraft();
            }
            // Enviamos el formulario inmediatamente
            document.getElementById('reportForm').submit();
        },

        normalizarTurno(t) {
            return String(t || '')
                .trim()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '');
        },

        calcularTiempoRestante() {
            const ahora = new Date(Date.now() + this.serverTimeOffset);
            const horaActual = ahora.getHours() * 60 + ahora.getMinutes();

            const turnoNorm = this.normalizarTurno(this.turnoActual);
            const esNoche = turnoNorm === 'noche';

            let finTurnoMin = 24 * 60;
            if (turnoNorm === 'dia' || turnoNorm === 'manana' || turnoNorm === 'mañana') finTurnoMin = 14 * 60;
            else if (turnoNorm === 'tarde') finTurnoMin = 22 * 60;
            else if (esNoche) finTurnoMin = 6 * 60;

            // El temporizador cuenta hasta el momento en que se habilita el botón (30 min antes)
            finTurnoMin -= 30;

            let minutosHastaFin;
            if (esNoche) {
                // Si la hora actual es en la tarde/noche (antes de medianoche)
                if (horaActual > 12 * 60) {
                    minutosHastaFin = (24 * 60 - horaActual) + finTurnoMin;
                } else {
                    // Si estamos en la madrugada/mañana (después de medianoche)
                    minutosHastaFin = finTurnoMin - horaActual;
                }
            } else {
                minutosHastaFin = finTurnoMin - horaActual;
            }

            this.tiempoRestanteMs = Math.max(0, minutosHastaFin) * 60 * 1000;
            this.enVentanaCierre = minutosHastaFin <= 0;
            this.puedeGuardar = this.enVentanaCierre || this.passwordBloqueada === 'unlocked';
        },

        bloquearManual() {
            this.passwordBloqueada = false;
            this.puedeGuardar = this.enVentanaCierre;
            this.mostrarPassword = false;
            this.verPassword = false;
            this.password = '';
            localStorage.removeItem('report_password_bloqueada');
            // Disparar evento de bloqueo
            window.dispatchEvent(new CustomEvent('unlock-status-changed', { detail: { unlocked: false } }));
        },

        verificarPassword() {
            if (this.password === this.passwordCorrecta) {
                this.puedeGuardar = true;
                this.passwordBloqueada = 'unlocked';
                this.mostrarPassword = false;
                this.verPassword = false;
                this.password = '';
                localStorage.setItem('report_password_bloqueada', 'unlocked');
                // Disparar evento de desbloqueo
                window.dispatchEvent(new CustomEvent('unlock-status-changed', { detail: { unlocked: true } }));
                Swal.fire('Éxito', 'Botón y campos habilitados correctamente', 'success');
            } else {
                this.intentosFallidos++;
                localStorage.setItem('report_intentos_fallidos', this.intentosFallidos);
                if (this.intentosFallidos >= 3) {
                    this.passwordBloqueada = true;
                    localStorage.setItem('report_password_bloqueada', 'true');
                    this.mostrarPassword = false;
                    this.verPassword = false;
                    Swal.fire('Bloqueado', 'Has superado los intentos permitidos', 'error');
                } else {
                    Swal.fire('Error', `Contraseña incorrecta. Quedan ${3 - this.intentosFallidos} intentos`, 'warning');
                    this.password = '';
                }
            }
        },

        get tiempoRestanteTexto() {
            const totalSegundos = Math.floor(this.tiempoRestanteMs / 1000);
            const h = Math.floor(totalSegundos / 3600);
            const m = Math.floor((totalSegundos % 3600) / 60);
            const s = totalSegundos % 60;
            return `${h}h ${m}m ${s}s`;
        }
    });
</script>

<!-- Cabecera Premium -->
<div class="bg-white rounded-2xl shadow-sm p-4 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
    <!-- Izquierda: Identidad -->
    <div class="flex items-center space-x-4">
        <div class="bg-blue-600 p-3.5 rounded-2xl text-white shadow-lg shadow-blue-100">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight text-uppercase">NUEVO REPORTE DIARIO</h2>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Distribución de Personal y Visualizaciones</p>
        </div>
    </div>

    <!-- Derecha: Controles y Tiempo -->
    <div class="flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-6"
         x-data="window.reportHeaderControls('{{ $turno }}')">
        <!-- Bloque Tiempo -->
        <div class="flex items-center space-x-3 bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-100">
            <div class="text-right">
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Tiempo para habilitar</div>
                <div class="text-sm font-black text-slate-700 font-mono" x-text="tiempoRestanteTexto"></div>
            </div>
            <div class="h-8 w-px bg-slate-200"></div>
            <div class="flex items-center space-x-2">
                <div x-show="!puedeGuardar && intentosFallidos < 3" class="flex items-center space-x-2">
                    <input type="checkbox" x-model="mostrarPassword" id="unlockCheckbox" class="w-4 h-4 text-blue-600 rounded-lg border-gray-300 focus:ring-blue-500">
                    <label for="unlockCheckbox" class="text-[10px] font-bold text-gray-500 uppercase cursor-pointer">Desbloquear</label>
                </div>
                <button type="button"
                        x-show="passwordBloqueada === 'unlocked' && !enVentanaCierre"
                        @click="bloquearManual()"
                        class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-800 transition-colors px-2 py-1 rounded-lg border border-slate-200 hover:border-slate-300">
                    Bloquear
                </button>
                <div x-show="mostrarPassword && !puedeGuardar" class="flex items-center space-x-1 animate-fadeIn">
                    <div class="relative">
                        <input :type="verPassword ? 'text' : 'password'"
                               x-model="password"
                               @keyup.enter="verificarPassword()"
                               placeholder="Clave"
                               class="text-xs px-2 py-1 pr-8 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 w-44">
                        <button type="button"
                                @click="verPassword = !verPassword"
                                class="absolute inset-y-0 right-1 flex items-center px-1.5 text-gray-400 hover:text-gray-600"
                                :title="verPassword ? 'Ocultar' : 'Mostrar'">
                            <svg x-show="!verPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="verPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.97 9.97 0 012.7-4.568m3.362-2.2A9.956 9.956 0 0112 5c4.478 0 8.269 2.943 9.543 7a9.972 9.972 0 01-4.132 5.411M15 12a3 3 0 00-3-3m0 0a3 3 0 013 3m-3-3L3 21m9-9a3 3 0 003 3"></path>
                            </svg>
                        </button>
                    </div>
                    <button type="button" @click="verificarPassword()" class="bg-blue-600 text-white p-1.5 rounded-lg">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Botón Guardar -->
        <div class="flex flex-col items-center space-y-1">
            <div class="flex items-center space-x-1.5 px-2 py-0.5 rounded-full border transition-all duration-300"
                 :class="hasUnsavedChanges ? 'bg-red-50 border-red-100 animate-pulse' : 'bg-gray-50 border-gray-100'">
                <svg class="w-3 h-3" :class="hasUnsavedChanges ? 'text-red-600' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span class="text-[9px] font-black uppercase tracking-tighter" 
                      :class="hasUnsavedChanges ? 'text-red-600' : 'text-gray-400'"
                      x-text="hasUnsavedChanges ? 'Protección Activa (X)' : 'Sin cambios pendientes'"></span>
            </div>
            <button type="button" 
                    @click="submitForm()"
                    :disabled="!puedeGuardar || isLiveSyncing"
                    :class="isLiveSyncing ? 'bg-amber-600/50 text-white/80 cursor-not-allowed shadow-none' : (isCollaborativeEditing ? 'bg-indigo-600 hover:bg-indigo-700 shadow-indigo-100' : (puedeGuardar ? 'bg-green-600 hover:bg-green-700 shadow-green-100' : 'bg-slate-200 text-slate-400 cursor-not-allowed'))"
                    class="px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest text-white transition-all shadow-xl active:scale-95 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                <span x-text="isLiveSyncing ? 'Monitoreo Activo' : (isCollaborativeEditing ? 'Guardar Cambios' : (puedeGuardar ? 'Finalizar Reporte' : 'En proceso...'))"></span>
            </button>
        </div>
    </div>
</div>
