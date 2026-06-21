@extends('layouts.app')

@section('title', 'Kilometrajes Finales - Seguridad Ciudadana')

@section('content')
<div class="max-w-full mx-auto" x-data="kilometrajeDashboard()">
    <!-- Cabecera Informativa -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3 rounded-lg text-white">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">SISTEMA DE MONITOREO SERENAZGO</h2>
                <p class="text-sm text-gray-500 font-medium">Control de KM, Auxilio Público y Parte de Ocurrencias</p>
            </div>
        </div>

        <div class="flex items-center space-x-4 bg-gray-50 px-4 py-2 rounded-xl border border-gray-100">
            <div class="text-right">
                <div class="text-lg font-bold text-blue-600">{{ $horaActual }} | {{ \Carbon\Carbon::parse($fechaActual)->format('d/m/Y') }}</div>
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $turno }}</div>
            </div>
            <div class="h-10 w-px bg-gray-200"></div>
            <div class="bg-white p-2 rounded-lg shadow-sm">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Buscador y Contadores -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model="search" placeholder="Filtrar por unidad o placa..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm">
            </div>
            <div class="flex items-center space-x-6">
                <!-- Contador Camionetas -->
                <div class="flex items-center space-x-2 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                    <div class="bg-blue-600 p-1.5 rounded-md text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-blue-400 uppercase leading-none block">PICK-UPS</span>
                        <div class="flex items-baseline space-x-1">
                            <span class="text-lg font-black text-blue-700 leading-none" x-text="assignedPickupCount">0</span>
                            <span class="text-xs font-bold text-blue-400">/</span>
                            <span class="text-sm font-bold text-blue-400" x-text="totalFleetPickups">0</span>
                        </div>
                    </div>
                </div>

                <!-- Contador AUTO -->
                <div class="flex items-center space-x-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
                    <div class="bg-slate-700 p-1.5 rounded-md text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17a2 2 0 11-4 0 2 2 0 014 0zM7 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase leading-none block">AUTOS</span>
                        <div class="flex items-baseline space-x-1">
                            <span class="text-lg font-black text-slate-700 leading-none" x-text="assignedAutoCount">0</span>
                            <span class="text-xs font-bold text-slate-400">/</span>
                            <span class="text-sm font-bold text-slate-400" x-text="totalFleetAutos">0</span>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-gray-200 mx-2"></div>

                <div class="flex items-center space-x-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-tighter">Sincronizado</span>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Wialon: Azul con Icono Naranja -->
            <button @click="window.open('https://hosting.wialon.us/?lang=es', '_blank')" class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md flex items-center justify-center space-x-2 group">
                <svg class="w-5 h-5 text-orange-400 group-hover:text-orange-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h1.5a3.5 3.5 0 013.5 3.5V17a4 4 0 01-4 4h-1.5a3.5 3.5 0 01-3.5-3.5V17a4 4 0 014-4h1.5"></path></svg>
                <span>Wialon</span>
            </button>
            <!-- SIPCOP-M: Rojo y Blanco (Perú) -->
            <button @click="window.open('https://seguridadciudadana.mininter.gob.pe/sipcop-m/reportes/mapa-recorrido-vehiculo', '_blank')" class="flex-1 bg-[#d91e18] text-white py-3 rounded-xl font-bold hover:bg-[#b91c1c] transition-all shadow-md flex items-center justify-center space-x-2 border-y-2 border-white/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <span>SIPCOP-M</span>
            </button>
        </div>
    </div>


    <!-- Contenido Principal -->
    <template x-if="unidades.length > 0">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100" id="capture-area">
            <div class="bg-[#1e293b] px-6 py-4 flex items-center justify-between">
                <div class="flex items-center space-x-2 text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <h3 class="font-bold uppercase tracking-wider text-sm">Reporte KM - {{ $turno }}</h3>
                </div>
                <div class="text-xs text-blue-300 font-bold uppercase tracking-widest">Metas: KM 90 | AP 230 | PO 3-10</div>
            </div>

            <div class="overflow-x-auto">
                <template x-if="filteredUnidades.length > 0">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 select-none">
                                <th @click="toggleSort('nombre')" class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-48 cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-1">
                                        <span>UNIDAD</span>
                                        <template x-if="sortCol === 'nombre'">
                                            <svg :class="sortDir === 'asc' ? '' : 'rotate-180'" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                        </template>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-48 text-center">TURNOS</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-40 text-center">JURISDICCIÓN</th>
                                <th @click="toggleSort('km')" class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-24 text-center cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-center space-x-1">
                                        <span>KM</span>
                                        <template x-if="sortCol === 'km'">
                                            <svg :class="sortDir === 'asc' ? '' : 'rotate-180'" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                        </template>
                                    </div>
                                </th>
                                <th @click="toggleSort('ap')" class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-24 text-center cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-center space-x-1">
                                        <span>A.P (min)</span>
                                        <template x-if="sortCol === 'ap'">
                                            <svg :class="sortDir === 'asc' ? '' : 'rotate-180'" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                        </template>
                                    </div>
                                </th>
                                <th @click="toggleSort('po')" class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-20 text-center cursor-pointer hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center justify-center space-x-1">
                                        <span>P.O</span>
                                        <template x-if="sortCol === 'po'">
                                            <svg :class="sortDir === 'asc' ? '' : 'rotate-180'" class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                        </template>
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-36 text-center">Estado KM</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-36 text-center">Estado AP</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-36 text-center">Estado PO</th>
                                <th class="px-4 py-3 text-[11px] font-black text-gray-500 uppercase tracking-wide w-24 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(unit, index) in filteredUnidades" :key="unit.id">
                                <tr :class="isComplete(unit) ? 'bg-emerald-50 hover:bg-emerald-100/50' : 'hover:bg-blue-50/30'" 
                                    class="transition-colors group">
                                    <!-- Unidad -->
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="font-bold text-gray-800 text-sm" x-text="`${unit.nombre} ${unit.placa}`"></span>
                                    </td>
                                    
                                    <!-- Matrícula (Placa) -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="checkbox" x-model="unit.turnos" value="NOCHE" class="w-3 h-3 text-blue-600 rounded">
                                                <span class="text-[10px] font-black text-gray-600">NOCHE</span>
                                            </label>
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="checkbox" x-model="unit.turnos" value="DIA" class="w-3 h-3 text-blue-600 rounded">
                                                <span class="text-[10px] font-black text-gray-600">DÍA</span>
                                            </label>
                                            <label class="flex items-center space-x-1 cursor-pointer">
                                                <input type="checkbox" x-model="unit.turnos" value="TARDE" class="w-3 h-3 text-blue-600 rounded">
                                                <span class="text-[10px] font-black text-gray-600">TARDE</span>
                                            </label>
                                        </div>
                                    </td>
                                    
                                    <!-- Último Registro -->
                                    <td class="px-4 py-3 text-center">
                                        <select x-model="unit.jurisdiccion" class="bg-gray-100 border border-gray-200 rounded text-[10px] font-black text-gray-700 px-2 py-1 outline-none focus:ring-1 focus:ring-blue-500 uppercase">
                                            <option value="SECTORIAL">SECTORIAL</option>
                                            <option value="T. ALTA">T. ALTA</option>
                                        </select>
                                    </td>
                                    
                                    <!-- KM Input -->
                                    <td class="px-4 py-3">
                                        <input type="number" x-model.number="unit.km" min="0" 
                                               class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-center font-bold text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all hover:border-gray-300">
                                    </td>
                                    
                                    <!-- AP Input -->
                                    <td class="px-4 py-3">
                                        <input type="number" x-model.number="unit.ap" min="0" 
                                               class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-center font-bold text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all hover:border-gray-300">
                                    </td>
                                    
                                    <!-- PO Input -->
                                    <td class="px-4 py-3">
                                        <input type="number" x-model.number="unit.po" min="0" max="10"
                                               class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1.5 text-center font-bold text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all hover:border-gray-300">
                                    </td>
                                    
                                    <!-- Estado KM -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center">
                                            <span :class="getEstadoKM(unit.km).clase" 
                                                  class="px-2 py-1 rounded-md text-xs font-bold text-center block w-full"
                                                  x-text="getEstadoKM(unit.km).texto">
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Estado AP -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center">
                                            <span :class="getEstadoAP(unit.ap).clase" 
                                                  class="px-2 py-1 rounded-md text-xs font-bold text-center block w-full"
                                                  x-text="getEstadoAP(unit.ap).texto">
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Estado PO -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center">
                                            <span :class="getEstadoPO(unit.po).clase" 
                                                  class="px-2 py-1 rounded-md text-xs font-bold text-center block w-full"
                                                  x-text="getEstadoPO(unit.po).texto">
                                            </span>
                                        </div>
                                    </td>
                                                                <!-- Acciones -->
                                    <td class="px-4 py-3 text-center">
                                        <button @click="unit.visible = !unit.visible" 
                                                :class="unit.visible ? 'text-blue-500 hover:text-blue-700 hover:bg-blue-50' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'"
                                                class="p-2 rounded-lg transition-all"
                                                :title="unit.visible ? 'Ocultar del reporte' : 'Mostrar en el reporte'">
                                            <!-- Icono de Ojo Abierto (Visible) -->
                                            <template x-if="unit.visible">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </template>
                                            <!-- Icono de Ojo Tachado (Oculto) -->
                                            <template x-if="!unit.visible">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 1.225 0 2.39.22 3.46.618M15 12a3 3 0 11-6 0 3 3 0 016 0zm-9.908 3.92L3 18l.88-3.032M17.657 16.657L13.414 20.9M15 12l5.25 5.25"></path>
                                                </svg>
                                            </template>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </template>

                <!-- Empty State for Search Results -->
                <template x-if="filteredUnidades.length === 0">
                    <div class="p-16 flex flex-col items-center justify-center text-center border-t border-gray-100">
                        <div class="bg-gray-50 p-6 rounded-full mb-6">
                            <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">No se encontraron unidades</h3>
                        <p class="text-gray-500 max-w-md mb-6">No hay resultados para la búsqueda "<strong x-text="search"></strong>".</p>
                        <button @click="search = ''" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span>Limpiar búsqueda</span>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Footer Acciones -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-3">
                    


                    <button @click="openWhatsAppModal()" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-green-700 transition-all shadow-md flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span>REPORTE WHATSAPP</span>
                    </button>
                </div>

                <button @click="captureScreen" class="bg-slate-500 text-white px-6 py-2.5 rounded-xl font-bold hover:bg-slate-600 transition-all shadow-md flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>CAPTURAR</span>
                </button>
            </div>
        </div>
    </template>

    <!-- Estado Vacío -->
    <template x-if="unidades.length === 0">
        <div class="bg-white rounded-2xl shadow-xl p-16 flex flex-col items-center justify-center text-center border border-dashed border-gray-300">
            <div class="bg-gray-50 p-6 rounded-full mb-6">
                <svg class="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No hay unidades vehiculares asignadas</h3>
            <p class="text-gray-500 max-w-md mb-6">Asigne unidades en <strong>Distribución del Personal de Campo</strong> para visualizarlas aquí.</p>
            <a href="{{ route('reportes.nuevo') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-md flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>Ir a Distribución de Personal</span>
            </a>
        </div>
    </template>

    <!-- Modal Reporte WhatsApp -->
    <div x-show="showWhatsAppModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
         @keydown.escape.window="closeWhatsAppModal()"
         x-cloak>
        
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all border border-gray-100"
             x-show="showWhatsAppModal"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            <!-- Header -->
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-blue-50 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="font-black text-xl text-gray-800 tracking-tight">Configuración de Reporte</h3>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-8 space-y-8">
                <!-- Fuente de datos -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        <label class="font-black text-gray-800 text-base">Fuente de datos:</label>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative cursor-pointer group">
                            <input type="radio" x-model="whatsappFuente" value="SIPCOP-M" class="hidden peer">
                            <div class="flex items-center justify-center space-x-2 border-2 border-gray-100 rounded-2xl p-4 peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:border-gray-200 transition-all duration-300">
                                <svg class="w-5 h-5 text-gray-400 peer-checked:text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>
                                <span class="font-bold text-gray-700">SIPCOP-M</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer group">
                            <input type="radio" x-model="whatsappFuente" value="Wialon" class="hidden peer">
                            <div class="flex items-center justify-center space-x-2 border-2 border-gray-100 rounded-2xl p-4 peer-checked:border-blue-600 peer-checked:bg-blue-50/50 hover:border-gray-200 transition-all duration-300">
                                <svg class="w-5 h-5 text-gray-400 peer-checked:text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 012 2v1.5a.5.5 0 00.5.5h.5a.5.5 0 00.5.5V13a3 3 0 01-3 3 3 3 0 01-3-3 3 3 0 01-3-3v-1.5a.5.5 0 00-.5-.5h-.5a.5.5 0 00-.5-.5v-1.5z" clip-rule="evenodd"></path></svg>
                                <span class="font-bold text-gray-700">Wialon</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Nota adicional -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <label class="font-black text-gray-800 text-base">Nota adicional (opcional):</label>
                    </div>
                    <textarea x-model="whatsappNota" 
                              class="w-full border-2 border-gray-100 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-0 focus:border-blue-600 outline-none resize-none transition-all"
                              rows="5"
                              maxlength="500"></textarea>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-8 pb-8 flex flex-col space-y-3">
                <button @click="generarReporteWhatsApp()" 
                        :disabled="!whatsappFuente || generandoReporte"
                        class="w-full bg-[#3498db] text-white py-4 rounded-xl font-black text-lg hover:bg-blue-600 transition-all shadow-xl shadow-blue-200 disabled:opacity-50 disabled:shadow-none flex items-center justify-center space-x-2">
                    <span x-show="!generandoReporte">Confirmar</span>
                    <svg x-show="generandoReporte" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
                <button @click="closeWhatsAppModal()" class="w-full py-2 text-gray-400 font-bold hover:text-gray-600 transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- html2canvas Library -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
function kilometrajeDashboard() {
    return {
        search: '',
        saving: false,
        unidades: @json($unidades).map(u => ({...u, visible: u.visible !== undefined ? u.visible : true})),
        
        generandoReporte: false,
        showWhatsAppModal: false,
        whatsappFuente: '',
        whatsappNota: '',
        turnoApp: '{{ $turno }}', // Mañana, Tarde, Noche
        totalFleetPickups: {{ $totalFlotaCamionetas ?? 0 }},
        totalFleetAutos: {{ $totalFlotaAutos ?? 0 }},
        sortCol: 'nombre',
        sortDir: 'asc',

        triggerNotification(msg, type = 'success') {
            window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: type } }));
        },

        toggleSort(col) {
            if (this.sortCol === col) {
                this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortCol = col;
                this.sortDir = 'asc';
            }
        },

        isComplete(unit) {
            const km = parseFloat(unit.km) || 0;
            const ap = parseFloat(unit.ap) || 0;
            const po = parseFloat(unit.po) || 0;
            return km >= 90 && ap >= 230 && po >= 3;
        },

        // Ordena los turnos en el orden correcto: NOCHE, DIA, TARDE
        sortTurnos(turnos) {
            const orden = ['NOCHE', 'DIA', 'TARDE'];
            const arr = Array.isArray(turnos) ? turnos : (turnos || '').split(',').filter(t => t.trim());
            return arr
                .map(t => t.trim())
                .sort((a, b) => orden.indexOf(a) - orden.indexOf(b))
                .join(', ');
        },
        
        init() {
            // Inicializar turnos y jurisdicción por defecto si están vacíos
            this.unidades.forEach(u => {
                // Convertir string de BD a array para Alpine
                if (u.turnos && typeof u.turnos === 'string') {
                    u.turnos = u.turnos.split(',').filter(t => t);
                } else if (!u.turnos) {
                    u.turnos = [];
                }

                // 1. Turnos por defecto si está vacío
                if (u.turnos.length === 0) {
                    if (this.turnoApp === 'Mañana') u.turnos = ['DIA'];
                    else if (this.turnoApp === 'Tarde') u.turnos = ['TARDE'];
                    else if (this.turnoApp === 'Noche') u.turnos = ['NOCHE'];
                }

                // 2. Jurisdicción por defecto basada en sector
                if (!u.jurisdiccion && u.sector) {
                    const sector = u.sector.toLowerCase();
                    if (sector.includes('norte') || sector.includes('centro')) {
                        u.jurisdiccion = 'SECTORIAL';
                    } else if (sector.includes('sur') || sector.includes('enace')) {
                        u.jurisdiccion = 'T. ALTA';
                    }
                }
            });

            // Auto-guardado (Watchers) con deep: true para detectar cambios en KM, AP, PO, etc.
            this.$watch('unidades', () => {
                clearTimeout(this._draftTimer);
                this._draftTimer = setTimeout(() => {
                    this.syncToDraft();
                }, 500);
            }, { deep: true });
        },

        // Sincronizar cambios en tiempo real (Borrador)
        async syncToDraft() {
            try {
                await fetch('/api/draft/kilometraje', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        unidades: this.unidades.map(u => ({
                            id: u.id,
                            km: u.km,
                            ap: u.ap,
                            po: u.po,
                            turnos: Array.isArray(u.turnos) ? u.turnos.join(',') : u.turnos,
                            jurisdiccion: u.jurisdiccion
                        }))
                    })
                });
                console.log('✅ Borrador actualizado');
            } catch (error) {
                console.error('Error auto-guardado:', error);
                this.triggerNotification('Error sincronizando auto-guardado', 'error');
            }
        },
        
        get assignedPickupCount() {
            return this.unidades.filter(u => {
                const tipo = (u.tipo || '').toUpperCase();
                return tipo.includes('PICK-UP') || tipo.includes('CAMIONETA') || tipo.includes('PICKUP');
            }).length;
        },

        get assignedAutoCount() {
            return this.unidades.filter(u => (u.tipo || '').toUpperCase().includes('AUTO')).length;
        },

        get filteredUnidades() {
            if (!this.unidades || this.unidades.length === 0) return [];
            
            let items = [...this.unidades];

            // 1. Filtro de búsqueda
            if (this.search) {
                const searchLower = this.search.toLowerCase();
                console.log('Searching for:', searchLower);
                items = items.filter(u => {
                    const nombre = (u.nombre || '').toLowerCase();
                    const placa = (u.placa || '').toLowerCase();
                    // Extraer el número de unidad (ignorar emojis al principio)
                    const nombreParts = nombre.split(' ');
                    const unitNumber = nombreParts.find(part => /\d/.test(part)) || '';
                    const matches = placa.includes(searchLower) || unitNumber.includes(searchLower) || nombre.includes(searchLower);
                    console.log('Unit:', u.nombre, u.placa, 'Unit Number:', unitNumber, 'Matches:', matches);
                    return matches;
                });
            }
            console.log('Filtered units:', items);

            // 2. Ordenamiento
            if (this.sortCol) {
                items.sort((a, b) => {
                    let valA = a[this.sortCol];
                    let valB = b[this.sortCol];
                    
                    // Manejo de valores numéricos
                    if (['km', 'ap', 'po'].includes(this.sortCol)) {
                        valA = parseFloat(valA) || 0;
                        valB = parseFloat(valB) || 0;
                    } else {
                        valA = (valA || '').toString().toLowerCase();
                        valB = (valB || '').toString().toLowerCase();
                    }

                    if (valA < valB) return this.sortDir === 'asc' ? -1 : 1;
                    if (valA > valB) return this.sortDir === 'asc' ? 1 : -1;
                    return 0;
                });
            }

            return items;
        },
        
        // === LÓGICA DE ESTADOS ===
        
        getEstadoKM(km) {
            if (km === null || km === '' || km === undefined) {
                return { 
                    texto: '⚠️ 90 KM requerido', 
                    clase: 'bg-yellow-100 text-yellow-700 border border-yellow-200' 
                };
            }
            km = parseFloat(km) || 0;
            if (km >= 100) {
                return { 
                    texto: `✅ COMPLETO (+${km - 90} KM)`, 
                    clase: 'bg-green-100 text-green-800 border border-green-200' 
                };
            }
            if (km >= 90) {
                return { 
                    texto: '✅ COMPLETO', 
                    clase: 'bg-green-100 text-green-800 border border-green-200' 
                };
            }
            return { 
                texto: `❌ FALTA ${90 - km} KM`, 
                clase: 'bg-red-100 text-red-700 border border-red-200' 
            };
        },
        
        getEstadoAP(ap) {
            if (ap === null || ap === '' || ap === undefined) {
                return { 
                    texto: '⚠️ 230 AP requerido', 
                    clase: 'bg-yellow-100 text-yellow-700 border border-yellow-200' 
                };
            }
            ap = parseInt(ap) || 0;
            if (ap >= 230) {
                return { 
                    texto: '✅ COMPLETO', 
                    clase: 'bg-green-100 text-green-800 border border-green-200' 
                };
            }
            const faltante = 230 - ap;
            const turnos = Math.ceil(faltante / 30);
            return { 
                texto: `❌ FALTA ${faltante} MIN (${turnos}T)`, 
                clase: 'bg-red-100 text-red-700 border border-red-200' 
            };
        },
        
        getEstadoPO(po) {
            if (po === null || po === '' || po === undefined) {
                return { 
                    texto: '⚠️ P.O requerido', 
                    clase: 'bg-yellow-100 text-yellow-700 border border-yellow-200' 
                };
            }
            po = parseInt(po) || 0;
            if (po > 10) {
                return { 
                    texto: '❌ PO máximo 10', 
                    clase: 'bg-red-100 text-red-700 border border-red-200' 
                };
            }
            if (po >= 3) {
                return { 
                    texto: `✅ COMPLETO (${po} P.O.)`, 
                    clase: 'bg-green-100 text-green-800 border border-green-200' 
                };
            }
            return { 
                texto: `❌ FALTAN ${3 - po} P.O.`, 
                clase: 'bg-red-100 text-red-700 border border-red-200' 
            };
        },
        
        // === ACCIONES ===
        
        async saveData() {
            if (this.unidades.length === 0) return;
            
            this.saving = true;

            try {
                // Validar PO máximo en frontend
                const invalidPO = this.unidades.some(u => u.po > 10);
                if (invalidPO) {
                    this.triggerNotification('El PO no puede ser mayor a 10 en algunas unidades', 'warning');
                    this.saving = false;
                    return;
                }

                const response = await fetch('{{ route('kilometrajes.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ 
                        reportes: this.unidades.map(u => ({
                            id: u.id,
                            km: parseFloat(u.km) || 0,
                            ap: parseInt(u.ap) || 0,
                            po: parseInt(u.po) || 0,
                            turnos: Array.isArray(u.turnos) ? u.turnos.join(',') : u.turnos,
                            jurisdiccion: u.jurisdiccion,
                            is_draft: u.is_draft || false
                        }))
                    })
                });

                const result = await response.json();
                
                if (response.ok) {
                    this.triggerNotification(result.message, 'success');
                } else {
                    throw new Error(result.message || 'Error del servidor');
                }
            } catch (error) {
                console.error('Error al guardar:', error);
                this.triggerNotification(error.message || 'No se pudo guardar la información', 'error');
            } finally {
                this.saving = false;
            }
        },

        clearForm() {
            if (this.unidades.every(u => u.km === 0 && u.ap === 0 && u.po === 0)) {
                return; // Ya está limpio
            }
            
            Swal.fire({
                title: '¿Limpiar formulario?',
                text: "Se pondrán a cero todos los valores de KM, AP y PO",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.unidades.forEach(u => {
                        u.km = 0;
                        u.ap = 0;
                        u.po = 0;
                    });
                    this.triggerNotification('Formulario limpio', 'success');
                }
            });
        },
        
        removeUnidad(id) {
            Swal.fire({
                title: '¿Eliminar unidad?',
                text: "Esta acción no se puede deshacer",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.unidades = this.unidades.filter(u => u.id !== id);
                }
            });
        },
        
        async loadLast() {
            try {
                const response = await fetch('{{ route('kilometrajes.last') }}', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error('No se pudo cargar el último registro');
                
                const data = await response.json();
                
                if (data.unidades && data.unidades.length > 0) {
                    // Actualizar valores con los del último registro
                    data.unidades.forEach(lastUnit => {
                        const unit = this.unidades.find(u => u.nombre === lastUnit.nombre || u.placa === lastUnit.placa);
                        if (unit) {
                            unit.km = lastUnit.km || 0;
                            unit.ap = lastUnit.ap || 0;
                            unit.po = lastUnit.po || 0;
                        }
                    });
                    
                    this.triggerNotification('Se han aplicado los valores del último registro', 'success');
                } else {
                    this.triggerNotification('No hay registros anteriores', 'info');
                }
            } catch (error) {
                console.error('Error:', error);
                this.triggerNotification('No se pudo cargar el último registro', 'error');
            }
        },

        getStyleFromClass(clase) {
            if (clase.includes('bg-green-100')) return 'background: #dcfce7; color: #166534; border: 1px solid #bbf7d0;';
            if (clase.includes('bg-red-100')) return 'background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;';
            if (clase.includes('bg-yellow-100')) return 'background: #fef9c3; color: #854d0e; border: 1px solid #fef08a;';
            return '';
        },

        async captureScreen() {
            this.triggerNotification('Generando captura...', 'info');

            const captureDiv = document.createElement('div');
            captureDiv.style.cssText = 'width:1200px;padding:40px;background:#f8fafc;position:fixed;left:-9999px;top:0;font-family:Arial,sans-serif;';

            let rowsHtml = '';
            this.unidades.filter(u => u.visible).forEach(u => {
                const turnosStr = this.sortTurnos(u.turnos);
                const estKm = this.getEstadoKM(u.km);
                const estAp = this.getEstadoAP(u.ap);
                const estPo = this.getEstadoPO(u.po);
                
                rowsHtml += `
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 15px; font-weight: bold; color: #1e293b; font-size: 14px;">${u.nombre} ${u.placa}</td>
                        <td style="padding: 12px 15px; text-align: center; font-size: 10px; font-weight: 800; color: #64748b;">${turnosStr}</td>
                        <td style="padding: 12px 15px; text-align: center; font-size: 11px; font-weight: 800; color: #1e293b;">${u.jurisdiccion || '---'}</td>
                        <td style="padding: 12px 15px; text-align: center; font-weight: bold; font-size: 14px;">${u.km || '0.00'}</td>
                        <td style="padding: 12px 15px; text-align: center; font-weight: bold; font-size: 14px;">${u.ap || '0'}</td>
                        <td style="padding: 12px 15px; text-align: center; font-weight: bold; font-size: 14px;">${u.po || '0'}</td>
                        <td style="padding: 8px;"><div style="padding: 6px; border-radius: 6px; font-size: 10px; font-weight: bold; text-align: center; ${this.getStyleFromClass(estKm.clase)}">${estKm.texto}</div></td>
                        <td style="padding: 8px;"><div style="padding: 6px; border-radius: 6px; font-size: 10px; font-weight: bold; text-align: center; ${this.getStyleFromClass(estAp.clase)}">${estAp.texto}</div></td>
                        <td style="padding: 8px;"><div style="padding: 6px; border-radius: 6px; font-size: 10px; font-weight: bold; text-align: center; ${this.getStyleFromClass(estPo.clase)}">${estPo.texto}</div></td>
                    </tr>
                `;
            });

            captureDiv.innerHTML = `
                <div style="background: #1e293b; color: white; padding: 25px; border-radius: 15px 15px 0 0; display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="margin: 0; font-size: 22px; font-weight: 800; text-transform: uppercase;">📊 REPORTE DE KILOMETRAJES - ${this.turnoApp}</h2>
                    <div style="font-weight: bold; color: #94a3b8; font-size: 13px;">Metas: KM 90 | AP 230 | PO 3-10</div>
                </div>
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 0 0 15px 15px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f1f5f9; text-align: left; border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase;">UNIDAD</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">TURNOS</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">JURISDICCIÓN</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">KM</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">A.P</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">P.O</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">ESTADO KM</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">ESTADO AP</th>
                            <th style="padding: 15px; font-size: 10px; color: #64748b; text-transform: uppercase; text-align: center;">ESTADO PO</th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
                <div style="margin-top: 15px; text-align: right; color: #64748b; font-size: 11px; font-weight: bold;">
                    Generado: ${new Date().toLocaleString()} | Seguridad Ciudadana
                </div>
            `;

            document.body.appendChild(captureDiv);

            try {
                const scale = Math.max(3, (window.devicePixelRatio || 1) * 2);
                const canvas = await html2canvas(captureDiv, {
                    backgroundColor: '#f8fafc',
                    scale,
                    useCORS: true,
                    allowTaint: false,
                    logging: false,
                    imageTimeout: 0,
                    width: captureDiv.offsetWidth,
                    height: captureDiv.offsetHeight,
                    windowWidth: captureDiv.offsetWidth,
                    windowHeight: captureDiv.offsetHeight,
                });
                const blob = await new Promise(r => canvas.toBlob(r, 'image/png', 1.0));
                const item = new ClipboardItem({ "image/png": blob });
                await navigator.clipboard.write([item]);
                document.body.removeChild(captureDiv);
                this.triggerNotification('La imagen limpia ha sido copiada al portapapeles', 'success');
            } catch (error) {
                console.error('Error:', error);
                if (captureDiv.parentNode) document.body.removeChild(captureDiv);
                this.triggerNotification('No se pudo generar la imagen', 'error');
            }
        },
        
        // === WHATSAPP MODAL ===
        
        async openWhatsAppModal() {
            const visibleUnits = this.unidades.filter(u => u.visible);
            if (visibleUnits.length === 0) {
                this.triggerNotification('No hay unidades visibles para generar el reporte', 'warning');
                return;
            }

            // PASO 1: Generar IMAGEN primero y copiarla al portapapeles
            this.triggerNotification('Generando imagen del reporte...', 'info');

            try {
                await this.generarImagenReporteLocal();

                // PASO 2: Mostrar mensaje de éxito de imagen
                this.triggerNotification('La imagen del reporte se copió al portapapeles. Ya puedes pegarla en WhatsApp', 'success');

                // PASO 3: Después de aceptar, mostrar modal de configuración
                this.showWhatsAppModal = true;
                this.whatsappFuente = '';
                this.whatsappNota = '';

            } catch (error) {
                console.error('Error generando imagen:', error);
                this.triggerNotification('No se pudo generar la imagen del reporte', 'error');
            }
        },
        
        closeWhatsAppModal() {
            this.showWhatsAppModal = false;
            this.whatsappFuente = '';
            this.whatsappNota = '';
        },
        
        // PASO 4: Al confirmar, solo generar TEXTO
        async generarReporteWhatsApp() {
            if (!this.whatsappFuente) {
                this.triggerNotification('Selecciona una fuente de datos', 'warning');
                return;
            }
            
            this.generandoReporte = true;
            
            try {
                const response = await fetch('{{ route('reporte.whatsapp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        fuente: this.whatsappFuente,
                        nota: this.whatsappNota,
                        unidades: this.unidades.filter(u => u.visible).map(u => ({
                            nombre: u.nombre,
                            placa: u.placa,
                            tipo: u.tipo,
                            km: parseFloat(u.km) || 0,
                            ap: parseInt(u.ap) || 0,
                            po: parseInt(u.po) || 0,
                            turnos: Array.isArray(u.turnos) ? u.turnos : (u.turnos || '').split(',').filter(t => t.trim())
                        }))
                    })
                });
                
                const data = await response.json();
                
                if (!data.success) throw new Error(data.message || 'Error al generar reporte');
                
                // Solo copiar TEXTO (la imagen ya fue copiada antes)
                await navigator.clipboard.writeText(data.texto);
                
                this.closeWhatsAppModal();
                
                // PASO 5: Mensaje de éxito del texto
                this.triggerNotification('El texto del reporte se ha copiado al portapapeles', 'success');
            } catch (error) {
                console.error('Error:', error);
                this.triggerNotification(error.message || 'No se pudo generar el reporte', 'error');
            } finally {
                this.generandoReporte = false;
            }
        },
        
        // Genera imagen profesional usando datos locales (sin necesidad de servidor)
        async generarImagenReporteLocal() {
            const captureDiv = document.createElement('div');
            captureDiv.style.cssText = 'width:900px;padding:50px;background:#fff;position:fixed;left:-9999px;top:0;font-family:Arial,sans-serif;';
            
            const fecha = new Date().toLocaleDateString('es-PE', { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' 
            }).toUpperCase();
            const turno = this.turnoApp;
            const usuario = '{{ strtoupper(Auth::user()->name ?? "SISTEMA") }}';
            
            const visibleUnits = this.unidades.filter(u => u.visible);
            const pickups = visibleUnits.filter(u => (u.tipo || '').toUpperCase().includes('CAMIONETA'));
            const autos = visibleUnits.filter(u => (u.tipo || '').toUpperCase().includes('AUTO'));
            
            const renderTable = (titulo, lista, colorHeader, colorBg) => {
                if (lista.length === 0) return '';
                let rows = '';
                lista.forEach(u => {
                    rows += `
                        <tr style="border-bottom:1px solid #ddd; background:${colorBg}; font-size:14px;">
                            <td style="padding:12px;text-align:center;font-weight:bold;width:25%;color:#000;">${u.nombre} ${u.placa}</td>
                            <td style="padding:12px;text-align:center;width:10%;font-weight:bold;color:#000;font-size:15px;">${u.km || '0.00'}</td>
                            <td style="padding:12px;text-align:center;width:10%;font-weight:bold;color:#000;font-size:15px;">${u.ap || '0'}</td>
                            <td style="padding:12px;text-align:center;width:10%;background:#fef9c3;font-weight:bold;color:#000;font-size:15px;">${u.po || '0'}</td>
                            <td style="padding:12px;text-align:center;width:25%;font-weight:bold;color:#000;font-size:15px;">${this.sortTurnos(u.turnos)}</td>
                            <td style="padding:12px;text-align:center;width:20%;font-weight:bold;color:#000;font-size:15px;">${u.jurisdiccion || ''}</td>
                        </tr>
                    `;
                });
                
                return `
                    <div style="margin-top:30px;">
                        <h3 style="text-align:center;color:${colorHeader === '#003399' ? '#003399' : '#e67e22'};text-transform:uppercase;margin-bottom:10px;font-size:20px;font-weight:bold;">${titulo}</h3>
                        <table style="width:100%;border-collapse:collapse;border:2px solid ${colorHeader};">
                            <thead>
                                <tr style="background:${colorHeader};color:white;text-transform:uppercase;font-size:15px;font-weight:bold;">
                                    <th style="padding:12px;font-weight:bold;">UNIDAD</th>
                                    <th style="padding:12px;font-weight:bold;">KM</th>
                                    <th style="padding:12px;font-weight:bold;">AP</th>
                                    <th style="padding:12px;font-weight:bold;">PO</th>
                                    <th style="padding:12px;font-weight:bold;">TURNO</th>
                                    <th style="padding:12px;font-weight:bold;">JURISDICCION</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `;
            };

            captureDiv.innerHTML = `
                <div style="display:flex;align-items:center;border-bottom:3px solid #003399;padding-bottom:15px;padding-top:10px;">
                    <img src="{{ asset('img/serenazgo_logo.png') }}" crossorigin="anonymous" style="height:140px;margin-right:30px;object-fit:contain;">
                    <div style="display:flex;flex-direction:column;justify-content:center;gap:6px;">
                        <h1 style="margin:0;color:#003399;font-size:38px;font-weight:bold;letter-spacing:1px;line-height:1;">TALARA</h1>
                        <p style="margin:0;font-size:20px;font-weight:bold;color:#333;line-height:1.2;">${fecha}</p>
                        <p style="margin:0;font-size:18px;color:#4b5563;font-weight:bold;line-height:1.2;">KM - 00:00 HRS / ${turno === 'Noche' ? '06:00' : turno === 'Mañana' ? '14:00' : '22:00'} HORAS</p>
                        <p style="margin:0;font-size:24px;color:#003399;font-weight:bold;text-transform:uppercase;line-height:1.2;">TURNO: ${turno === 'Mañana' ? 'DÍA' : turno}</p>
                    </div>
                </div>
                
                ${renderTable('CAMIONETAS PICK-UP', pickups, '#003399', '#f0f7ff')}
                ${renderTable('AUTOS SEDAN', autos, '#e67e22', '#fffaf0')}
                
                <div style="margin-top:40px;display:flex;justify-content:space-between;font-size:12px;color:#666;font-weight:bold;border-top:1px solid #ddd;padding-top:15px;">
                    <span>📅 ${new Date().toLocaleString('es-PE')}</span>
                    <span>👤 REGISTRO: ${usuario}</span>
                </div>
            `;
            
            document.body.appendChild(captureDiv);
            await new Promise(r => setTimeout(r, 300));

            const logoImg = captureDiv.querySelector('img');
            if (logoImg) {
                await new Promise(resolve => {
                    if (logoImg.complete) {
                        resolve();
                        return;
                    }
                    logoImg.onload = () => resolve();
                    logoImg.onerror = () => resolve();
                });
            }

            const scale = Math.max(3, (window.devicePixelRatio || 1) * 2);
            const canvas = await html2canvas(captureDiv, {
                backgroundColor: '#ffffff',
                scale,
                useCORS: true,
                allowTaint: false,
                logging: false,
                imageTimeout: 0,
                width: captureDiv.offsetWidth,
                height: captureDiv.offsetHeight,
                windowWidth: captureDiv.offsetWidth,
                windowHeight: captureDiv.offsetHeight,
            });
            const blob = await new Promise(r => canvas.toBlob(r, 'image/png', 1.0));
            const item = new ClipboardItem({ "image/png": blob });
            await navigator.clipboard.write([item]);

            document.body.removeChild(captureDiv);
        }
    };
}
</script>

<style>
[x-cloak] { display: none !important; }

/* Quitar flechas de input number */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type=number] {
    -moz-appearance: textfield;
}

/* Hover en filas */
tbody tr:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

/* Animación suave para badges */
[class*="bg-green-100"], [class*="bg-red-100"] {
    transition: all 0.3s ease;
}
</style>
@endsection
