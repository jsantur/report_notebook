@extends('layouts.app')

@section('title', 'Backups - Seguridad Ciudadana')

@section('content')
<div class="max-w-7xl mx-auto" x-data="backupModule(@js($backups))">
    <!-- Cabecera Premium -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3.5 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Backups</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Administra y genera respaldos de la base de datos del sistema.</p>
            </div>
        </div>
        
        <div class="flex items-center space-x-3 w-full lg:w-auto">
            <!-- Botón Restaurar desde archivo -->
            <button @click="showRestoreModal = true; restoreMode = 'upload'; restoreTargetName = ''" 
                    class="w-full lg:w-auto px-6 py-4 bg-amber-500 text-white rounded-[24px] font-black text-sm shadow-xl shadow-amber-100 hover:bg-amber-600 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                RESTAURAR ARCHIVO
            </button>
            <!-- Botón Crear -->
            <button @click="createBackup" 
                    :disabled="isProcessing"
                    class="w-full lg:w-auto px-10 py-4 bg-blue-600 text-white rounded-[24px] font-black text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!isProcessing" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                <svg x-cloak x-show="isProcessing" class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span x-text="isProcessing ? 'GENERANDO...' : 'CREAR NUEVA COPIA'"></span>
            </button>
        </div>
    </div>

    <!-- Tabla de Backups -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Nombre del Archivo</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center w-40">Fecha</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center w-32">Tamaño</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center w-48">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <template x-for="backup in backups" :key="backup.name">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span class="font-bold text-gray-700 tracking-wide" x-text="backup.name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-medium text-gray-500" x-text="backup.date"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded bg-gray-100 text-xs font-black text-gray-600 border border-gray-200" x-text="backup.size"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Descargar -->
                                    <button @click="downloadBackup(backup.name)" 
                                        class="p-2 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors"
                                        title="Descargar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </button>
                                    <!-- Eliminar -->
                                    <button @click="deleteBackup(backup.name)" 
                                            class="p-2 rounded bg-red-50 text-red-600 hover:bg-red-500 hover:text-white transition-colors"
                                            title="Eliminar"
                                            :disabled="isProcessing">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    
                    <tr x-cloak x-show="backups.length === 0">
                        <td colspan="4" class="p-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="font-bold tracking-wide">NO HAY BACKUPS</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════ -->
    <!--  MODAL DE RESTAURACIÓN CON CONTRASEÑA             -->
    <!-- ══════════════════════════════════════════════════ -->
    <div x-cloak x-show="showRestoreModal" 
         @keydown.escape.window="showRestoreModal && closeRestoreModal()"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <!-- Overlay (no cierra al hacer click) -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
        
        <!-- Card -->
        <div class="relative bg-[#1e293b] rounded-2xl shadow-2xl w-full max-w-lg p-8 border border-slate-600/30"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            
            <!-- Close -->
            <button @click="closeRestoreModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            
            <!-- Header -->
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tight">Restaurar Base de Datos</h3>
                    <p class="text-xs text-slate-400 font-medium">Ingrese la contraseña de seguridad para continuar</p>
                </div>
            </div>

            <!-- Modo: Subir archivo -->
            <template x-if="restoreMode === 'upload'">
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Archivo de backup (.sqlite)</label>
                    <div class="relative">
                        <input type="file" accept=".sqlite,.db" @change="handleFileSelect($event)"
                               class="w-full px-4 py-3 bg-slate-700/50 border border-slate-500/30 rounded-xl text-white text-sm file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-amber-500 file:text-white file:font-bold file:text-xs file:cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500/50">
                    </div>
                    <p x-show="uploadFileName" class="mt-2 text-xs text-emerald-400 font-medium" x-text="'✓ ' + uploadFileName"></p>
                </div>
            </template>

            <!-- Modo: Servidor -->
            <template x-if="restoreMode === 'server'">
                <div class="mb-5 px-4 py-3 bg-slate-700/30 rounded-xl border border-slate-600/30">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Archivo seleccionado</p>
                    <p class="text-sm text-white font-bold" x-text="restoreTargetName"></p>
                </div>
            </template>

            <!-- Campo contraseña -->
            <div class="mb-6" x-data="{ showPassword: false }">
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">Contraseña de restauración</label>
                <div class="relative">
                    <input :type="showPassword ? 'text' : 'password'" x-model="restorePassword" @keydown.enter="executeRestore()"
                           placeholder="Ingrese la contraseña"
                           class="w-full px-4 py-3 pr-12 bg-slate-700/50 border border-slate-500/30 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500/50 transition-all">
                    <!-- Toggle ojo -->
                    <button type="button" @click="showPassword = !showPassword" 
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 transition-colors focus:outline-none">
                        <!-- Ojo abierto -->
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <!-- Ojo cerrado -->
                        <svg x-cloak x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <p x-show="restoreError" class="mt-2 text-xs text-red-400 font-medium" x-text="restoreError"></p>
            </div>

            <!-- Advertencia -->
            <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-xl">
                <p class="text-xs text-red-300 font-bold">⚠️ ADVERTENCIA: La base de datos actual será reemplazada. Se creará un respaldo automático del estado anterior.</p>
            </div>

            <!-- Botones -->
            <div class="flex gap-3">
                <button @click="closeRestoreModal()" class="flex-1 px-6 py-3 bg-slate-600/50 text-slate-300 rounded-xl font-bold text-sm hover:bg-slate-600 transition-colors">
                    CANCELAR
                </button>
                <button @click="executeRestore()" :disabled="isProcessing || !restorePassword || (restoreMode === 'upload' && !uploadFile)"
                        class="flex-1 px-6 py-3 bg-amber-500 text-white rounded-xl font-bold text-sm hover:bg-amber-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <svg x-cloak x-show="isProcessing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isProcessing ? 'RESTAURANDO...' : 'RESTAURAR'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.triggerNotification = function(mensaje, type = 'success') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true, position: 'top-end', showConfirmButton: false,
                timer: 3000, timerProgressBar: true, icon: type, title: mensaje,
                background: '#1e293b', color: '#ffffff',
                iconColor: type === 'success' ? '#38bdf8' : '#ef4444'
            });
        }
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('backupModule', (initialBackups) => ({
            isProcessing: false,
            backups: initialBackups || [],
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

            // Restore modal state
            showRestoreModal: false,
            restoreMode: 'server',       // 'server' | 'upload'
            restoreTargetName: '',
            restorePassword: '',
            restoreError: '',
            uploadFile: null,
            uploadFileName: '',

            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    this.uploadFile = file;
                    this.uploadFileName = file.name;
                } else {
                    this.uploadFile = null;
                    this.uploadFileName = '';
                }
            },

            closeRestoreModal() {
                this.showRestoreModal = false;
                this.restorePassword = '';
                this.restoreError = '';
                this.uploadFile = null;
                this.uploadFileName = '';
            },

            createBackup() {
                this.isProcessing = true;
                fetch('{{ route("backups.store") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken }
                })
                .then(r => r.json())
                .then(data => {
                    this.isProcessing = false;
                    if (data.success) {
                        triggerNotification(data.message);
                        if (data.backup) this.backups = [data.backup, ...this.backups];
                    } else {
                        triggerNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(() => { this.isProcessing = false; triggerNotification('Error al comunicarse con el servidor', 'error'); });
            },

            downloadBackup(name) {
                const link = document.createElement('a');
                link.href = `/backups/${name}/download`;
                link.download = name;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                // Mostrar notificación con ruta de descarga
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    icon: 'success',
                    title: '✅ Archivo descargado exitosamente',
                    html: '<span style="font-size:12px;color:#94a3b8;">📂 Ubicación: <b>Descargas / Downloads</b> de su navegador</span>',
                    background: '#1e293b',
                    color: '#ffffff',
                    iconColor: '#38bdf8'
                });
            },

            deleteBackup(name) {
                Swal.fire({
                    title: '¿Eliminar backup?', text: 'No se podrá recuperar este archivo',
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#334155', confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar', background: '#1e293b', color: '#fff'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.isProcessing = true;
                        fetch(`{{ url('/backups') }}/${name}`, {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken }
                        })
                        .then(r => r.json())
                        .then(data => {
                            this.isProcessing = false;
                            if (data.success) {
                                triggerNotification(data.message);
                                this.backups = this.backups.filter(b => b.name !== name);
                            } else {
                                triggerNotification('Error: ' + data.message, 'error');
                            }
                        })
                        .catch(() => { this.isProcessing = false; triggerNotification('Error de conexión', 'error'); });
                    }
                });
            },

            executeRestore() {
                if (!this.restorePassword) { this.restoreError = 'Ingrese la contraseña'; return; }
                this.restoreError = '';
                this.isProcessing = true;

                if (this.restoreMode === 'upload') {
                    // ── Upload mode ──
                    const formData = new FormData();
                    formData.append('backup_file', this.uploadFile);
                    formData.append('password', this.restorePassword);

                    fetch('{{ route("backups.restore.upload") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.isProcessing = false;
                        if (data.success) {
                            this.closeRestoreModal();
                            triggerNotification(data.message);
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            this.restoreError = data.message;
                        }
                    })
                    .catch(() => { this.isProcessing = false; this.restoreError = 'Error de conexión'; });
                } else {
                    // ── Server backup mode ──
                    fetch(`{{ url('/backups/restore') }}/${this.restoreTargetName}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                        body: JSON.stringify({ password: this.restorePassword })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.isProcessing = false;
                        if (data.success) {
                            this.closeRestoreModal();
                            triggerNotification(data.message);
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            this.restoreError = data.message;
                        }
                    })
                    .catch(() => { this.isProcessing = false; this.restoreError = 'Error de conexión'; });
                }
            }
        }));
    });
</script>
@endpush
@endsection
