@extends('layouts.app')

@section('title', 'Nuevo Reporte - Seguridad Ciudadana')

@section('content')
<script>
    window.camarasFromDB = @json($camaras->map(function($c) { return ['name' => $c->nombre, 'operativa' => true]; }));
</script>

<div id="reporte-main" class="max-w-7xl mx-auto" 
     @ocurrencia-deleted.window="eliminarOcurrenciaSilencioso()" 
     @toggle-operador.window="toggleOperador($event.detail)"
     @visualizacion-saved.window="handleVisualizacionSaved($event.detail)"
     @unidades-report-saved.window="handleUnidadesReportSaved($event.detail)"
     @campo-record-saved.window="handleCampoRecordSaved($event.detail)"
     x-data="reporteData({
        turno: '{{ $turno }}',
        defaultSupervisorCamarasId: '{{ $defaultSupervisorCamarasId ?? '' }}',
        adminMonitoringUserId: '{{ $adminMonitoringUserId ?? '' }}',
        adminMonitoringMode: '{{ $adminMonitoringMode ?? '' }}',
        adminMonitoringDraftId: '{{ $adminMonitoringDraftId ?? '' }}',
        csrf_token: '{{ csrf_token() }}',
        routes: {
            serenazgo_search: '{{ route('api.serenazgo.search') }}',
            ai_correct: '{{ route('ai.correct') }}'
        },
        maquinas: ['Operador01', 'Operador02', 'Operador03', 'Operador04', 'Operador05', 'Operador06', 'Operador07', 'Operador08', 'Operador09', 'Operador10', 'Operador11', 'Operador12'],
        unidades: {
            @foreach($vehiculos->where('tipo_patrullaje', 'Vehicular') as $v)
                '{{ str_replace('Unidad ', '', $v->nro_unidad) }}': { placa: '{{ $v->placa }}', tipo: '{{ $v->tipo }}', emoji: '🚘' },
            @endforeach
        },
        motorizadas: {
            @foreach($vehiculos->where('tipo_patrullaje', 'Motorizado') as $v)
                '{{ str_replace('Unidad ', '', $v->nro_unidad) }}': { placa: '{{ $v->placa }}' },
            @endforeach
        }
     })">
     
    <x-report-header :turno="$turno" />

    <!-- Banner de Sincronización en Vivo para Administradores -->
    <template x-if="currentUserRole === 'admin' && activeDrafts.length > 0">
        <div class="mb-6 p-5 rounded-2xl border bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-xl flex flex-col md:flex-row justify-between items-center gap-4 transition-all duration-300">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-white/20 rounded-xl animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-base">Monitoreo en Tiempo Real Disponible ⚡</h4>
                    <p class="text-xs text-white/80">Se detectó un borrador activo del supervisor en la base de datos para este turno.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <template x-for="draft in activeDrafts" :key="draft.id">
                    <div class="flex items-center gap-3 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl border border-white/10 transition-colors">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold" x-text="draft.user_name"></span>
                            <span class="text-[10px] text-white/70" x-text="'Actualizado: ' + new Date(draft.updated_at).toLocaleTimeString()"></span>
                        </div>
                        <template x-if="selectedDraftId === draft.id && isLiveSyncing">
                            <button type="button" @click="stopLiveSync()" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg font-bold shadow transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                                Detener Monitoreo
                            </button>
                        </template>
                        <template x-if="selectedDraftId === draft.id && isCollaborativeEditing">
                            <button type="button" @click="stopCollaborativeEdit()" class="bg-amber-500 hover:bg-amber-600 text-white text-xs px-3 py-1.5 rounded-lg font-bold shadow transition-colors flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                                Terminar Edición
                            </button>
                        </template>
                        <template x-if="selectedDraftId !== draft.id">
                            <div class="flex items-center gap-2">
                                <button type="button" @click="startLiveSync(draft)" class="bg-white text-indigo-700 hover:bg-indigo-50 text-xs px-3 py-1.5 rounded-lg font-bold shadow transition-colors">
                                    Monitorear
                                </button>
                                <button type="button" @click="startCollaborativeEdit(draft)" class="bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg font-bold shadow transition-colors border border-indigo-400">
                                    Modificar
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </template>



    <!-- Tarjeta Principal del Formulario -->
    <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100 relative">
        <!-- Glassmorphic Overlay for Live Monitoring (Read-Only Mode) -->
        <div x-show="isLiveSyncing" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute inset-0 bg-slate-900/10 backdrop-blur-[2px] rounded-2xl z-40 flex items-center justify-center p-4 text-center">
            <div class="bg-white/95 backdrop-blur-md p-6 rounded-2xl shadow-2xl border border-slate-200/50 max-w-md transform transition-all">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-slate-800 font-extrabold text-lg mb-2">Monitoreo en Tiempo Real Activo</h3>
                <p class="text-slate-500 text-xs leading-relaxed mb-4">
                    Estás viendo el llenado del supervisor en vivo (Modo Solo Lectura). 
                    Si necesitas realizar correcciones en este borrador, haz clic en el botón de abajo.
                </p>
                <button type="button" @click="startCollaborativeEdit(activeDrafts.find(d => d.id === selectedDraftId))" class="bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs px-5 py-2.5 rounded-xl transition-all shadow-md active:scale-95">
                    Habilitar Edición Colaborativa ✏️
                </button>
            </div>
        </div>

        <form action="{{ route('reportes.store') }}" method="POST" id="reportForm">
            @csrf
            
            <!-- Fila 1: Responsable, Fecha, Hora, Turno -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div class="relative border-b-2 border-blue-100 focus-within:border-blue-500 transition-colors">
                    <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Responsable del Cuaderno</label>
                    <input type="text" value="{{ Auth::user()->name }}" class="w-full bg-transparent py-1 focus:outline-none text-gray-700 font-medium uppercase" readonly>
                </div>
                <div class="relative border-b-2 border-blue-100 focus-within:border-blue-500 transition-colors">
                    <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Fecha</label>
                    <input :type="camposFechaHoraTurnoDesbloqueados ? 'date' : 'text'" name="fecha" id="fecha" x-model="fechaActual" class="w-full bg-transparent py-1 focus:outline-none text-gray-700 font-medium" :readonly="!camposFechaHoraTurnoDesbloqueados">
                </div>
                <div class="relative border-b-2 border-blue-100 focus-within:border-blue-500 transition-colors">
                    <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Hora</label>
                    <input :type="camposFechaHoraTurnoDesbloqueados ? 'time' : 'text'" step="1" name="hora" id="hora" x-model="horaActual" class="w-full bg-transparent py-1 focus:outline-none text-gray-700 font-medium" :readonly="!camposFechaHoraTurnoDesbloqueados">
                </div>
                <div class="relative border-b-2 border-blue-100 focus-within:border-blue-500 transition-colors">
                    <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Turno</label>
                    <!-- Campo de texto cuando está bloqueado -->
                    <input 
                        type="text" 
                        x-model="turnoActual" 
                        x-show="!camposFechaHoraTurnoDesbloqueados"
                        class="w-full bg-transparent py-1 focus:outline-none text-gray-700 font-medium" 
                        readonly
                    >
                    <!-- Lista desplegable cuando está desbloqueado -->
                    <select 
                        name="turno"
                        x-model="turnoActual"
                        x-show="camposFechaHoraTurnoDesbloqueados"
                        class="w-full bg-transparent py-1 focus:outline-none text-gray-700 font-medium"
                    >
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                    </select>
                    <!-- Campo hidden para enviar cuando está bloqueado -->
                    <input type="hidden" name="turno" x-model="turnoActual">
                </div>
            </div>

            <!-- Fila 2: Supervisores -->
            <x-reporte-seccion-supervisores :supervisoresCampo="$supervisoresCampo" :supervisoresCamaras="$supervisoresCamaras" />

            <!-- Botones de Categorías -->
            <div class="space-y-4">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="text-lg font-bold text-gray-700">Secciones del Reporte</h3>
                    <template x-if="localStorage.getItem('reporte_draft')">
                        <span class="text-xs text-green-600 font-medium flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Borrador guardado
                        </span>
                    </template>
                </div>

                <x-reporte-seccion-ocurrencias />
                <x-reporte-seccion-camaras />
                <x-reporte-seccion-campo />
                <x-reporte-seccion-patrullaje />
                <x-reporte-seccion-visualizaciones />
            </div>
        </form>
    </div>



    <x-modal-lista-campo />
    <x-modal-gestion-camaras />
</div>

<x-modal-campo :vehiculos="$vehiculos" />
<x-modal-operadores />
<x-modal-unidades />
<x-modal-visualizaciones />
<x-modal-ocurrencias />

@push('styles')
    @include('reportes.partials.styles-nuevo')
@endpush

@push('scripts')
    @include('reportes.partials.scripts-nuevo')
@endpush

@endsection
