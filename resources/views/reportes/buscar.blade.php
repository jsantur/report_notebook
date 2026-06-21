@extends('layouts.app')

@section('title', 'Buscar Reportes - Seguridad Ciudadana')

@section('content')
<div class="max-w-7xl mx-auto" x-data="reporteSearch()">
    <!-- Cabecera Premium (Estilo Kilometrajes) -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <!-- Izquierda: Identidad -->
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3.5 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Historial de Reportes</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Consulta y gestión de registros emitidos</p>
            </div>
        </div>

        <!-- Derecha: Buscador y Filtros -->
        <div class="flex flex-wrap items-end gap-3 w-full lg:w-auto justify-end">
            <!-- Buscador -->
            <div class="flex flex-col space-y-1 w-full sm:w-64">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Supervisor</label>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" x-model="searchTerm" placeholder="Buscar supervisor..." 
                           class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-transparent transition-all outline-none text-sm font-medium">
                </div>
            </div>

            <!-- Turno -->
            <div class="flex flex-col space-y-1 w-full sm:w-auto">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Filtrar Turno</label>
                <div class="relative group">
                    <select x-model="filterTurno" 
                            class="w-full sm:w-36 pl-4 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none shadow-sm text-sm font-bold text-slate-600 transition-all appearance-none cursor-pointer">
                        <option value="">Turno</option>
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Rango de Fechas -->
            <div class="flex flex-col space-y-1 w-full sm:w-auto">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Rango de Fechas</label>
                <div class="flex items-center space-x-2">
                    <div class="relative group">
                        <input type="date" x-model="filterDateStart" title="Fecha inicio"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none shadow-sm text-sm font-bold text-slate-600 transition-all">
                    </div>
                    <span class="text-slate-400 font-bold">-</span>
                    <div class="relative group">
                        <input type="date" x-model="filterDateEnd" title="Fecha fin" :min="filterDateStart"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none shadow-sm text-sm font-bold text-slate-600 transition-all">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Reportes -->
    <div class="space-y-6">
        <!-- Lista de Reportes en Tabla Premium -->
        <div x-show="filteredReportes.length > 0" class="bg-white rounded-[32px] border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-black uppercase tracking-widest">
                            <th class="px-8 py-5">Fecha / Hora</th>
                            <th class="px-6 py-5">Turno</th>
                            <th class="px-6 py-5">Supervisor Cámaras</th>
                            <th class="px-6 py-5">Supervisor Campo</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="reporte in filteredReportes" :key="reporte.id">
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <!-- Fecha / Hora -->
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-blue-50 text-blue-600 p-2.5 rounded-xl shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-slate-800" x-text="reporte.fecha"></div>
                                            <div class="text-[11px] font-bold text-slate-400 mt-0.5" x-text="`Registrado a las: ${reporte.hora}`"></div>
                                        </div>
                                    </div>
                                </td>
                                <!-- Turno -->
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span :class="{
                                        'bg-amber-50 text-amber-700 border-amber-100/70': reporte.turno === 'Mañana',
                                        'bg-blue-50 text-blue-700 border-blue-100/70': reporte.turno === 'Tarde',
                                        'bg-indigo-50 text-indigo-700 border-indigo-100/70': reporte.turno === 'Noche'
                                    }" class="px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider border" x-text="reporte.turno"></span>
                                </td>
                                <!-- Supervisor Cámaras -->
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-bold text-slate-700" x-text="(reporte.supervisores_camaras_list || []).map(s => s.nombres + ' ' + s.apellido_paterno).join(' / ') || 'N/A'"></span>
                                    </div>
                                </td>
                                <!-- Supervisor Campo -->
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="text-sm font-bold text-slate-700" x-text="reporte.supervisor_campo?.nombres + ' ' + reporte.supervisor_campo?.apellido_paterno || 'N/A'"></span>
                                    </div>
                                </td>
                                <!-- Acciones -->
                                <td class="px-8 py-5 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button @click="openDetails(reporte)" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg shadow-blue-100 transition-all hover:-translate-y-0.5 active:translate-y-0">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Detalles
                                        </button>
                                        @if(Auth::user()->role === 'admin')
                                        <button @click="deleteReport(reporte.id)" class="inline-flex items-center p-2 bg-red-50 hover:bg-red-500 text-red-600 hover:text-white rounded-xl font-bold text-xs border border-red-100 transition-all hover:-translate-y-0.5 active:translate-y-0 shadow-sm" title="Eliminar reporte">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="filteredReportes.length === 0" class="text-center py-24 bg-white rounded-[40px] border-2 border-dashed border-gray-100 shadow-sm">
            <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800">No se encontraron reportes</h3>
            <p class="text-gray-400 mt-2 max-w-xs mx-auto">Intenta ajustar los filtros de búsqueda o la fecha para encontrar lo que buscas.</p>
        </div>

        <!-- Pagination Links -->
        <div class="mt-8">
            {{ $reportes->links() }}
        </div>
    </div>

    <!-- Modal de Detalles -->
    <div x-show="showModal" 
         @keydown.escape.window="if (editingItemId !== null || editingAsigId !== null) { editingItemId = null; editingAsigId = null; } else { showModal = false; }"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
        
        <div class="bg-slate-50 w-full max-w-5xl max-h-[92vh] rounded-[40px] shadow-2xl overflow-hidden flex flex-col relative z-10 border border-white/20"
             x-transition:enter="transition ease-out duration-400"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            
            <!-- Header Modal -->
            <div class="bg-white border-b border-gray-100 px-10 py-8 flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Detalle del Reporte</h2>
                    <div class="flex flex-wrap items-center gap-4 mt-3 text-sm">
                        <span class="flex items-center font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-xl">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span x-text="selectedReporte?.fecha"></span>
                        </span>
                        <span class="flex items-center font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-xl">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-text="selectedReporte?.hora"></span>
                        </span>
                        <span class="bg-indigo-600 text-white px-3 py-1 rounded-xl font-black text-xs uppercase" x-text="selectedReporte?.turno"></span>
                    </div>
                </div>
                <button @click="showModal = false" class="bg-gray-100 text-gray-500 hover:text-gray-800 p-3 rounded-2xl hover:bg-gray-200 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Contenido Modal -->
            <div class="flex-1 overflow-y-auto px-10 py-8 space-y-12 scrollbar-thin bg-slate-50">
                
                <!-- Banner de Solo Lectura -->
                <div x-show="!puedeEditar" class="bg-amber-50 border border-amber-200 rounded-3xl p-5 flex items-start space-x-4 shadow-sm" x-cloak>
                    <div class="bg-amber-100 text-amber-700 p-2.5 rounded-2xl flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-amber-800 uppercase tracking-wide">Reporte de Solo Lectura</h4>
                        <p class="text-xs text-amber-600 font-bold mt-1 leading-relaxed">
                            Este cuaderno pertenece a <strong class="text-amber-800 uppercase" x-text="selectedReporte?.user ? selectedReporte.user.name : 'otro supervisor'"></strong> y está protegido contra modificaciones. Si necesitas delegar la autoría o corregir datos, contacta con un administrador del sistema.
                        </p>
                    </div>
                </div>

                <!-- Info Responsable y Reasignación -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                    <div class="flex items-center space-x-3">
                        <div class="bg-slate-100 text-slate-600 p-2.5 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Responsable del Cuaderno</p>
                            <p class="text-sm font-black text-slate-700 uppercase" x-text="selectedReporte?.user ? selectedReporte.user.name : 'Sin Responsable Asignado'"></p>
                        </div>
                    </div>

                    <!-- Menú de Reasignación sólo para Administradores -->
                    <div x-show="currentUserRole === 'admin'" class="flex items-center space-x-2 bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-100" x-cloak>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Delegar a:</label>
                        <select @change="reasignarResponsable($event.target.value)" 
                                class="bg-white border border-slate-200 rounded-xl px-3 py-1.5 text-xs font-bold text-slate-600 outline-none focus:ring-2 focus:ring-blue-500 transition-all cursor-pointer">
                            <option value="">Seleccionar Usuario</option>
                            <template x-for="u in users" :key="u.id">
                                <option :value="u.id" :selected="selectedReporte?.user_id === u.id" x-text="u.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Info Supervisores -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-5">
                        <div class="bg-blue-100 w-14 h-14 rounded-2xl flex items-center justify-center text-blue-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Supervisor Cámaras</p>
                            <p class="text-lg font-black text-slate-800" x-text="(selectedReporte?.supervisores_camaras_list || []).map(s => s.nombres + ' ' + s.apellido_paterno).join(' / ')"></p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center space-x-5">
                        <div class="bg-emerald-100 w-14 h-14 rounded-2xl flex items-center justify-center text-emerald-600">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Supervisor Campo</p>
                            <p class="text-lg font-black text-slate-800" x-text="selectedReporte?.supervisor_campo?.nombres + ' ' + selectedReporte?.supervisor_campo?.apellido_paterno"></p>
                        </div>
                    </div>
                </div>

                <!-- Ocurrencias de Relevo -->
                <section>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-2 h-8 bg-red-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Ocurrencias de Relevo:</h3>
                    </div>
                    <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm relative">
                        <div class="absolute top-6 right-8 opacity-10">
                            <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21L14.017 18C14.017 16.899 14.918 16 16.017 16L19.017 16C19.57 16 20.017 15.553 20.017 15V14L14.017 14L14.017 4L22.017 4L22.017 15C22.017 16.657 20.674 18 19.017 18L17.017 18L17.017 21L14.017 21ZM2.017 21L2.017 18C2.017 16.899 2.918 16 4.017 16L7.017 16C7.57 16 8.017 15.553 8.017 15V14L2.017 14L2.017 4L10.017 4L10.017 15C10.017 16.657 8.674 18 7.017 18L5.017 18L5.017 21L2.017 21Z"></path></svg>
                        </div>
                        <div class="text-slate-700 leading-relaxed whitespace-pre-wrap font-medium" x-text="selectedReporte?.ocurrencias_relevo || 'Sin novedades registradas.'"></div>
                    </div>
                </section>

                <!-- Operadores de Cámaras -->
                <section>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-2 h-8 bg-indigo-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Distribución Personal Cámaras:</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="op in parseJSON(selectedReporte?.distribucion_personal_camaras)" :key="op.id">
                            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm flex flex-col space-y-3">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-indigo-600 text-white w-10 h-10 rounded-2xl flex items-center justify-center font-black text-xs shadow-lg shadow-indigo-100" x-text="op.maquina.replace('Operador', 'M')"></div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-black text-slate-800 truncate" x-text="`${op.nombres} ${op.apellido_paterno}`"></p>
                                        <p class="text-[10px] text-indigo-400 font-black uppercase tracking-widest" x-text="op.maquina"></p>
                                    </div>
                                </div>
                                <div x-show="op.camaras && op.camaras.length > 0" class="pt-3 border-t border-slate-50">
                                    <p class="text-[9px] text-slate-400 font-black uppercase tracking-tighter mb-1">Cámaras Asignadas:</p>
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="cam in op.camaras" :key="cam">
                                            <span class="text-[8px] bg-slate-50 text-slate-500 px-1.5 py-0.5 rounded border border-slate-100 font-bold uppercase" x-text="cam"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Distribución de Personal de Campo -->
                <section>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Distribución Personal Campo:</h3>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <template x-for="item in (selectedReporte?.distribucion_personal_campo || [])" :key="item.id">
                            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm">
                                <div class="flex items-start space-x-5">
                                    <div class="bg-slate-50 w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shadow-inner" x-text="item.tipo_patrullaje === 'Vehicular' ? '🚔' : (item.tipo_patrullaje === 'Motorizado' ? '🏍️' : '👮')"></div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-black text-slate-800 text-lg leading-tight">
                                                    <template x-if="item.unidad">
                                                        <span x-text="`Unidad Móvil ${item.unidad}`"></span>
                                                    </template>
                                                    <template x-if="!item.unidad && item.tipo_patrullaje !== 'A pie'">
                                                        <span x-text="item.descripcion"></span>
                                                    </template>
                                                    <template x-if="!item.unidad && item.tipo_patrullaje === 'A pie'">
                                                        <span>
                                                            <span class="text-slate-500 text-sm font-bold">N° de Puesto: </span>
                                                            <span class="text-blue-600 font-black uppercase" x-text="`${item.descripcion} (${item.cantidad || 1})`"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                                <p class="text-xs font-black text-blue-500 uppercase tracking-widest mt-1" x-text="item.tipo_patrullaje"></p>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl uppercase tracking-widest border border-slate-200" x-text="`Sector: ${item.ubicacion}`"></span>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-x-4 gap-y-2">
                                            <div x-show="item.chofer">
                                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-tighter">Chofer</p>
                                                <p class="text-xs font-bold text-slate-700 truncate" x-text="item.chofer"></p>
                                            </div>
                                            <div x-show="item.operador">
                                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-tighter">Operador</p>
                                                <p class="text-xs font-bold text-slate-700 truncate" x-text="item.operador"></p>
                                            </div>
                                            <div x-show="item.lince">
                                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-tighter">Lince</p>
                                                <p class="text-xs font-bold text-slate-700 truncate" x-text="item.lince"></p>
                                            </div>
                                            <div x-show="item.sereno">
                                                <p class="text-[9px] text-gray-400 font-black uppercase tracking-tighter">Personal</p>
                                                <p class="text-xs font-bold text-slate-700 truncate" x-text="item.sereno"></p>
                                            </div>
                                        </div>

                                        <!-- Nueva sección para COD. PO (Replicando Halcón) -->
                                        <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between" 
                                             x-show="
                                                item.tipo_patrullaje === 'A pie' 
                                                ? (selectedReporte?.distribucion_personal_campo || []).find(i => i.tipo_patrullaje === 'A pie' && (i.cantidad || 1) == (item.cantidad || 1))?.id === item.id
                                                : (item.tipo_patrullaje === 'Motorizado' || (item.descripcion && item.descripcion.toUpperCase().includes('SIERRA BRAVO')))
                                             ">
                                            <div class="flex flex-wrap gap-1.5">
                                                <template x-for="code in (item.cod_po || '').split(',').filter(c => c)" :key="code">
                                                    <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-lg text-[9px] font-black border border-indigo-100" x-text="code"></span>
                                                </template>
                                                <span x-show="!item.cod_po" class="text-[10px] text-gray-300 font-bold uppercase italic">Sin códigos</span>
                                            </div>
                                            <div class="relative">
                                                <button x-show="puedeEditar" @click="editItemCodes(item)" class="text-blue-500 hover:text-blue-700 p-1.5 bg-blue-50 rounded-xl transition-all hover:scale-110 active:scale-95 shadow-sm" x-cloak>
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Popover de ingreso de códigos -->
                                                <div x-show="editingItemId === item.id" 
                                                     class="absolute z-[110] bg-white p-4 rounded-3xl shadow-2xl border border-gray-100 w-[240px] right-0 bottom-full mb-3"
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                                     x-transition:enter-end="opacity-100 scale-100 translate-y-0">
                                                    
                                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Ingresar Códigos (Max 3)</p>
                                                    <div class="space-y-2">
                                                        <input type="text" x-model="tempItemCodes[0]" :name="`cod_po[${item.id}][]`" placeholder="Ej. PO-12906226" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold placeholder:text-gray-300">
                                                        <input type="text" x-model="tempItemCodes[1]" :name="`cod_po[${item.id}][]`" placeholder="Segundo código" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold placeholder:text-gray-300">
                                                        <input type="text" x-model="tempItemCodes[2]" :name="`cod_po[${item.id}][]`" placeholder="Tercer código" class="w-full text-xs px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none font-bold placeholder:text-gray-300">
                                                    </div>
                                                    <div class="flex justify-end space-x-2 mt-4">
                                                        <button @click="editingItemId = null" class="px-3 py-2 text-[10px] font-black text-slate-400 hover:text-slate-600 uppercase tracking-tighter">Cancelar</button>
                                                        <button @click="saveItemCodes(item)" class="px-4 py-2 text-[10px] font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-lg shadow-indigo-100 uppercase transition-all active:scale-95">Aplicar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Reportes de Unidades Halcón -->
                <section>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-2 h-8 bg-amber-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Reporte de Unidades Halcón:</h3>
                    </div>
                    <div class="space-y-4">
                        <template x-for="rep in parseJSON(selectedReporte?.reporte_personal_patrullando)" :key="rep.id">
                            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden group">
                                <button @click="rep.open = !rep.open" class="w-full flex items-center justify-between p-6 hover:bg-slate-50 transition-all text-left">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-amber-100 p-3 rounded-2xl text-amber-600 shadow-sm">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 text-lg">Reporte de las <span x-text="rep.hora"></span></p>
                                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest" x-text="`${rep.unidadesCount || 0} Unidades reportadas`"></p>
                                        </div>
                                    </div>
                                    <div class="bg-slate-100 p-2 rounded-xl text-slate-400 group-hover:text-slate-600 transition-colors">
                                        <svg class="w-5 h-5 transition-transform duration-300" :class="rep.open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </button>
                                <div x-show="rep.open" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-4"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="p-8 bg-slate-50 border-t border-gray-50 text-sm text-slate-700 whitespace-pre-wrap font-mono leading-relaxed tracking-tight" 
                                     x-text="rep.detalles"></div>
                            </div>
                        </template>
                        <div x-show="!selectedReporte?.reporte_personal_patrullando || parseJSON(selectedReporte?.reporte_personal_patrullando).length === 0" class="text-center py-10 bg-gray-50 rounded-[32px] border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-bold">No hay reportes de unidades registrados.</p>
                        </div>
                    </div>
                </section>

                <!-- Visualizaciones Resaltantes -->
                <section>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-2 h-8 bg-purple-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Visualizaciones Resaltantes (IA):</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="vis in parseJSON(selectedReporte?.visualizaciones_resaltantes)" :key="vis.id">
                            <div class="bg-white p-6 rounded-[32px] border border-gray-100 shadow-sm relative overflow-hidden group hover:border-purple-200 transition-colors">
                                <div class="absolute top-0 left-0 w-1.5 h-full" :class="vis.is_ai ? 'bg-purple-600 shadow-[0_0_15px_rgba(147,51,234,0.5)]' : 'bg-slate-300'"></div>
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-[10px] font-black text-white bg-slate-800 px-2 py-0.5 rounded-lg tracking-widest" x-text="vis.hora"></span>
                                            <span x-show="vis.is_ai" class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.828 6.535a1 1 0 00-1.414-1.414L3.707 5.828a1 1 0 001.414 1.414l.707-.707zM17 10a1 1 0 100-2h-1a1 1 0 100 2h1zM12.828 14.828a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 100-2H4a1 1 0 100 2h1zM6.535 14.172a1 1 0 101.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM10 11a1 1 0 100-2 1 1 0 000 2zM14.535 4.465a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707z"></path></svg>
                                                IA OK
                                            </span>
                                        </div>
                                        <h4 class="font-black text-slate-800 mt-2 text-lg leading-tight" x-text="vis.camara"></h4>
                                    </div>
                                </div>
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <p class="text-xs text-slate-600 leading-relaxed font-medium italic" x-text="vis.corregido || vis.original"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Kilometrajes Registrados -->
                <section>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-2 h-8 bg-emerald-500 rounded-full"></div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Kilometrajes Registrados (Halcón):</h3>
                    </div>
                    <div class="bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="bg-slate-900 text-white">
                                        <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px]">Unidad</th>
                                        <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px]">Placa</th>
                                        <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px] text-center">KM Final</th>
                                        <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px] text-center">Recorrido A/P</th>
                                        <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px] text-center">Total P/O</th>
                                        <th class="px-6 py-4 font-black uppercase tracking-widest text-[10px]">Cód. PO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="asig in selectedReporte?.asignaciones" :key="asig.id">
                                        <tr class="border-b border-gray-50 hover:bg-slate-50 transition-colors group">
                                            <td class="px-6 py-5">
                                                <span class="bg-slate-100 text-slate-700 w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm group-hover:bg-blue-600 group-hover:text-white transition-all" x-text="asig.unidad_id"></span>
                                            </td>
                                            <td class="px-6 py-5 text-slate-500 font-black tracking-tighter" x-text="asig.placa"></td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="text-lg font-black text-blue-600" x-text="asig.km"></span>
                                                <span class="text-[9px] text-gray-400 font-bold block">KILÓMETROS</span>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="text-lg font-black text-emerald-600" x-text="asig.ap"></span>
                                                <span class="text-[9px] text-gray-400 font-bold block">KM RECORRIDO</span>
                                            </td>
                                            <td class="px-6 py-5 text-center">
                                                <span class="text-lg font-black text-indigo-600" x-text="asig.po"></span>
                                                <span class="text-[9px] text-gray-400 font-bold block">PUNTOS</span>
                                            </td>
                                            <td class="px-6 py-5 relative">
                                                <div x-show="editingAsigId !== asig.id" class="flex items-center justify-between group/edit">
                                                    <div class="flex flex-wrap gap-1.5 max-w-[150px]">
                                                        <template x-for="code in (asig.cod_po || '').split(',').filter(c => c)" :key="code">
                                                            <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-lg text-[9px] font-black border border-indigo-100" x-text="code"></span>
                                                        </template>
                                                        <span x-show="!asig.cod_po" class="text-gray-300 italic text-[10px] font-medium">Ninguno</span>
                                                    </div>
                                                    <button x-show="puedeEditar" @click="editCodes(asig)" class="opacity-0 group-hover/edit:opacity-100 text-blue-500 hover:text-blue-700 transition-opacity p-1 bg-blue-50 rounded-lg" x-cloak>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </button>
                                                </div>
                                                <div x-show="editingAsigId === asig.id" class="flex flex-col space-y-2 absolute z-10 bg-white p-3 rounded-xl shadow-xl border border-gray-100 -translate-y-1/2 top-1/2 right-0 w-[240px]">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase">Ingresar Códigos (Max 3)</p>
                                                    <input type="text" x-model="tempCodes[0]" placeholder="Ej. PO-12906226" class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded focus:ring-1 focus:ring-indigo-500 outline-none">
                                                    <input type="text" x-model="tempCodes[1]" placeholder="Segundo código" class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded focus:ring-1 focus:ring-indigo-500 outline-none">
                                                    <input type="text" x-model="tempCodes[2]" placeholder="Tercer código" class="w-full text-xs px-2 py-1.5 border border-gray-200 rounded focus:ring-1 focus:ring-indigo-500 outline-none">
                                                    <div class="flex justify-end space-x-2 pt-1">
                                                        <button @click="editingAsigId = null" class="px-3 py-1 text-[10px] font-bold text-gray-500 hover:text-gray-700 bg-gray-100 rounded">Cancelar</button>
                                                        <button @click="saveCodes(asig)" class="px-3 py-1 text-[10px] font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded shadow-sm">Aplicar</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Footer Modal -->
            <div class="bg-white border-t border-gray-100 px-10 py-8 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                <button @click="showModal = false" class="px-8 py-3 text-gray-400 font-black hover:text-gray-800 transition-colors uppercase tracking-widest text-xs">Cerrar detalle</button>
                <div class="flex space-x-4 w-full sm:w-auto">
                    <button x-show="puedeEditar" @click="saveAllChanges()" class="flex-1 sm:flex-none inline-flex items-center justify-center px-8 py-4 bg-blue-600 text-white rounded-[24px] font-black text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-0.5 active:translate-y-0 uppercase tracking-wider" x-cloak>
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                        GUARDAR CAMBIOS
                    </button>
                    <!-- Botón PDF -->
                    <button @click="generatePDF(selectedReporte)" 
                            class="flex items-center justify-center w-14 h-14 bg-red-500 text-white rounded-2xl shadow-lg shadow-red-200 hover:bg-red-600 transition-all hover:-translate-y-1 active:translate-y-0 group" 
                            title="Descargar Reporte PDF">
                        <svg class="w-7 h-7 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </button>

                    <!-- Botón EXCEL -->
                    <button @click="generateExcel(selectedReporte)" 
                            class="flex items-center justify-center w-14 h-14 bg-emerald-500 text-white rounded-2xl shadow-lg shadow-emerald-200 hover:bg-emerald-600 transition-all hover:-translate-y-1 active:translate-y-0 group" 
                            title="Descargar Reporte Excel">
                        <svg class="w-7 h-7 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function reporteSearch() {
        return {
            reportes: @json($reportes->items()),
            searchTerm: '',
            filterTurno: '',
            filterDateStart: '',
            filterDateEnd: '',
            showModal: false,
            selectedReporte: null,
            editingAsigId: null,
            editingItemId: null,
            tempCodes: ['', '', ''],
            tempItemCodes: ['', '', ''],
            currentUserId: {{ Auth::id() }},
            currentUserRole: '{{ Auth::user()->role }}',
            users: @json($users),

            get puedeEditar() {
                if (!this.selectedReporte) return false;
                // Modificado para permitir que usuarios de otros turnos ingresen códigos PO
                return true;
            },

            get filteredReportes() {
                return this.reportes.filter(r => {
                    const camarasNames = (r.supervisores_camaras_list || []).map(s => s.nombres + ' ' + s.apellido_paterno).join(' ');
                    const supervisores = (
                        (r.supervisor_campo?.nombres || '') + ' ' + 
                        (r.supervisor_campo?.apellido_paterno || '') + ' ' +
                        camarasNames
                    ).toLowerCase();
                    
                    const matchesSearch = !this.searchTerm || supervisores.includes(this.searchTerm.toLowerCase());
                    const matchesTurno = !this.filterTurno || r.turno === this.filterTurno;
                    const matchesDateStart = !this.filterDateStart || r.fecha >= this.filterDateStart;
                    const matchesDateEnd = !this.filterDateEnd || r.fecha <= this.filterDateEnd;
                    
                    return matchesSearch && matchesTurno && matchesDateStart && matchesDateEnd;
                });
            },

            editCodes(asig) {
                this.editingAsigId = asig.id;
                const codes = (asig.cod_po || '').split(',').map(c => c.trim()).filter(c => c);
                this.tempCodes = [
                    codes[0] || '',
                    codes[1] || '',
                    codes[2] || ''
                ];
            },

            saveCodes(asig) {
                const validCodes = this.tempCodes.map(c => c.trim()).filter(c => c);
                asig.cod_po = validCodes.join(',');
                this.editingAsigId = null;
            },
            
            triggerNotification(msg, type = 'success') {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: type } }));
            },

            editItemCodes(item) {
                this.editingItemId = item.id;
                const codes = (item.cod_po || '').split(',').map(c => c.trim()).filter(c => c);
                this.tempItemCodes = [
                    codes[0] || '',
                    codes[1] || '',
                    codes[2] || ''
                ];
            },

            saveItemCodes(item) {
                const validCodes = this.tempItemCodes.map(c => c.trim()).filter(c => c);
                item.cod_po = validCodes.join(',');
                this.editingItemId = null;
            },

            async saveAllChanges() {
                if (!this.selectedReporte) return;
                
                if (!this.puedeEditar) {
                    this.triggerNotification('No tienes permisos para editar este reporte.', 'error');
                    return;
                }
                
                try {
                    const response = await axios.post('{{ route('asignaciones.updateCodes') }}', {
                        reporte_id: this.selectedReporte.id,
                        distribucion_personal_campo: JSON.stringify(this.selectedReporte.distribucion_personal_campo),
                        asignaciones: this.selectedReporte.asignaciones.map(a => ({
                            id: a.id,
                            cod_po: a.cod_po
                        }))
                    });

                    if (response.data.success) {
                        // Actualizar localmente el reporte original en el listado para reflejo inmediato sin recargar la página
                        const idx = this.reportes.findIndex(r => r.id === this.selectedReporte.id);
                        if (idx !== -1) {
                            this.reportes[idx].distribucion_personal_campo = JSON.stringify(this.selectedReporte.distribucion_personal_campo);
                            this.reportes[idx].asignaciones = JSON.parse(JSON.stringify(this.selectedReporte.asignaciones));
                        }

                        this.triggerNotification('Los códigos se actualizaron correctamente.', 'success');
                    }
                } catch (error) {
                    this.triggerNotification('No se pudieron guardar los cambios.', 'error');
                }
            },

            async reasignarResponsable(userId) {
                if (!userId || !this.selectedReporte) return;
                
                try {
                    const response = await axios.post(`/reportes/${this.selectedReporte.id}/reasignar`, {
                        user_id: userId
                    });

                    if (response.data.success) {
                        this.selectedReporte.user_id = parseInt(userId);
                        this.selectedReporte.user = response.data.user;

                        const idx = this.reportes.findIndex(r => r.id === this.selectedReporte.id);
                        if (idx !== -1) {
                            this.reportes[idx].user_id = parseInt(userId);
                            this.reportes[idx].user = response.data.user;
                        }

                        this.triggerNotification(response.data.message, 'success');
                    }
                } catch (error) {
                    this.triggerNotification(error.response?.data?.message || 'No se pudo reasignar el responsable.', 'error');
                }
            },

            openDetails(reporte) {
                // Preparar datos JSON para Alpine
                const rep = JSON.parse(JSON.stringify(reporte));
                
                // Parsear distribuciones para manipulación directa
                rep.distribucion_personal_campo = this.parseJSON(rep.distribucion_personal_campo);

                // Asegurar que los reportes de patrullaje tengan la propiedad 'open' para el acordeón
                if (rep.reporte_personal_patrullando) {
                    try {
                        let data = JSON.parse(rep.reporte_personal_patrullando);
                        if (Array.isArray(data)) {
                            data = data.map(d => ({ ...d, open: false }));
                            rep.reporte_personal_patrullando = JSON.stringify(data);
                        }
                    } catch(e) {}
                }

                this.selectedReporte = rep;
                this.showModal = true;
            },

            parseJSON(json) {
                if (!json) return [];
                try {
                    return typeof json === 'string' ? JSON.parse(json) : json;
                } catch (e) {
                    return [];
                }
            },

            async deleteReport(id) {
                const result = await Swal.fire({
                    title: '<span class="text-slate-800">¿Estás seguro?</span>',
                    html: '<p class="text-gray-500 font-medium">Esta acción eliminará permanentemente el reporte y todas sus asignaciones asociadas.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Sí, eliminar ahora',
                    cancelButtonText: 'Cancelar',
                    background: '#ffffff',
                    customClass: {
                        popup: 'rounded-[40px] p-10',
                        confirmButton: 'rounded-2xl px-8 py-4 font-black uppercase text-xs tracking-widest shadow-lg shadow-red-100',
                        cancelButton: 'rounded-2xl px-8 py-4 font-black uppercase text-xs tracking-widest'
                    }
                });

                if (result.isConfirmed) {
                    try {
                        const response = await axios.delete(`/reportes/${id}`);
                        if (response.data.success) {
                            this.reportes = this.reportes.filter(r => r.id !== id);
                            Swal.fire({
                                title: '¡Eliminado!',
                                text: 'El reporte ha sido borrado del sistema.',
                                icon: 'success',
                                customClass: { popup: 'rounded-[40px]' }
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo completar la operación. Inténtalo de nuevo.',
                            icon: 'error',
                            customClass: { popup: 'rounded-[40px]' }
                        });
                    }
                }
            },

            generatePDF(reporte) {
                if (!reporte || !reporte.id) return;
                
                const url = `{{ url('/reportes') }}/${reporte.id}/pdf`;
                window.open(url, '_blank');
            },

            generateExcel(reporte) {
                if (!reporte || !reporte.id) return;
                
                Swal.fire({
                    title: '¡Descargando Excel!',
                    html: '<div class="text-xs font-semibold text-slate-300">El reporte se está procesando y se guardará en tu carpeta de <b>Descargas</b>.</div>',
                    icon: 'success',
                    timer: 4000,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    timerProgressBar: true,
                    background: '#1e293b',
                    color: '#ffffff',
                    iconColor: '#10b981',
                    customClass: {
                        popup: 'rounded-2xl p-4 border border-slate-700/50 shadow-2xl'
                    }
                });

                const url = `{{ url('/reportes') }}/${reporte.id}/excel`;
                window.location.href = url;
            }
        }
    }
</script>

<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 8px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 20px;
        border: 2px solid #f8fafc;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #cbd5e1;
    }
    
    [x-cloak] { display: none !important; }
</style>
@endsection
