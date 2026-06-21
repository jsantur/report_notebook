@extends('layouts.app')

@section('title', 'Configuración de Sistema - Seguridad Ciudadana')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <!-- Cabecera Premium (Estilo Kilometrajes) -->
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-10 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <!-- Izquierda: Identidad -->
        <div class="flex items-center space-x-5">
            <div class="bg-blue-600 p-4 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight uppercase">Configuración de Reportes</h1>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Gestión de turnos, horarios y frecuencias</p>
            </div>
        </div>

        <!-- Derecha: Estado -->
        <div class="flex items-center space-x-4 bg-slate-50 px-6 py-3 rounded-2xl border border-slate-100 shadow-inner">
            <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado del Sistema</span>
                <span class="text-xs font-bold text-emerald-500 uppercase tracking-widest flex items-center">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse mr-2"></span>
                    Operativo y Activo
                </span>
            </div>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 p-6 rounded-2xl flex items-center space-x-4 shadow-sm animate-fade-in">
            <div class="bg-emerald-500 p-2 rounded-full text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-emerald-800 font-bold">{{ session('status') }}</p>
        </div>
    @endif

    <form action="{{ route('configuracion.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(['DIA', 'TARDE', 'NOCHE'] as $turno)
                @php $data = $settings[$turno] ?? []; @endphp
                <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-500 group">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <span class="px-4 py-1.5 rounded-xl text-xs font-black uppercase tracking-widest {{ $turno === 'DIA' ? 'bg-amber-100 text-amber-600' : ($turno === 'TARDE' ? 'bg-orange-100 text-orange-600' : 'bg-slate-800 text-white') }}">
                                {{ $turno }}
                            </span>
                            <div class="bg-slate-50 p-2 rounded-xl text-slate-400 group-hover:text-blue-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Horario de Turno</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <input type="time" name="settings[{{$turno}}][start]" value="{{ $data['start'] ?? '' }}" class="bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500">
                                    <input type="time" name="settings[{{$turno}}][end]" value="{{ $data['end'] ?? '' }}" class="bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Frecuencia (minutos)</label>
                                <input type="number" name="settings[{{$turno}}][frequency]" value="{{ $data['frequency'] ?? '' }}" class="w-full bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Horas de Notificación</label>
                                <textarea name="settings[{{$turno}}][notifications]" 
                                          class="w-full bg-slate-50 border-none rounded-2xl text-sm font-mono font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 h-24"
                                          placeholder="08:00, 09:00, ...">{{ implode(', ', $data['notifications'] ?? []) }}</textarea>
                                <p class="text-[9px] text-slate-400 mt-2 italic font-medium">* Separar horas por coma</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
            <div class="flex items-start space-x-4">
                <div class="bg-indigo-50 p-3 rounded-2xl text-indigo-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-800">Recordatorio Automático</p>
                    <p class="text-xs text-slate-500 font-medium max-w-md">El sistema detectará automáticamente el turno actual y lanzará notificaciones en las horas especificadas.</p>
                </div>
            </div>
            <button type="submit" class="w-full md:w-auto px-10 py-5 bg-blue-600 text-white rounded-[24px] font-black text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-1 active:translate-y-0">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>

@endsection
