<!-- Modal de Gestión y Asignación de Cámaras -->
<div x-show="showCamarasAsignacionModal" 
     @keydown.escape.window="showCamarasAsignacionModal = false" 
     class="fixed inset-0 z-[60] flex items-start justify-center p-4 pt-6 sm:pt-10 bg-black/60 backdrop-blur-md overflow-y-auto" x-cloak x-transition>
    <div class="bg-white w-full max-w-5xl rounded-[32px] shadow-2xl overflow-hidden flex flex-col h-[90vh]" @click.outside.prevent>
        <!-- Header -->
        <div class="p-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="bg-blue-600 p-2.5 rounded-2xl text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">Gestión de Cámaras</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Estado y Distribución de Dispositivos (Filtrado: CSV)</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Botón de Refrescar -->
                <button 
                    @click="cargarCamarasDesdeCSV()"
                    :disabled="cargandoCamarasCSV"
                    class="p-2 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
                    <svg 
                        class="w-5 h-5" 
                        :class="{'animate-spin': cargandoCamarasCSV}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="text-xs font-bold" x-text="cargandoCamarasCSV ? 'Actualizando...' : 'Refrescar'"></span>
                </button>
                <button @click="showCamarasAsignacionModal = false" class="bg-white p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Dashboard de Resumen -->
        <div class="px-8 py-4 bg-white flex items-center justify-between border-b border-slate-50">
            <div class="flex space-x-4">
                <!-- Tarjeta: Total -->
                <div class="bg-slate-50 px-4 py-2.5 rounded-2xl border border-slate-100 flex flex-col items-center min-w-[100px]">
                    <span class="text-[9px] text-slate-400 font-black uppercase tracking-tighter">Registradas</span>
                    <span class="text-xl font-black text-slate-800" x-text="camarasList.length"></span>
                </div>
                <!-- Tarjeta: Operativas -->
                <div class="bg-green-50 px-4 py-2.5 rounded-2xl border border-green-100 flex flex-col items-center min-w-[100px]">
                    <span class="text-[9px] text-green-500 font-black uppercase tracking-tighter">Operativas</span>
                    <span class="text-xl font-black text-green-600" x-text="camarasList.filter(c => c.operativa).length"></span>
                </div>
                <!-- Tarjeta: Inoperativas -->
                <div class="bg-red-50 px-4 py-2.5 rounded-2xl border border-red-100 flex flex-col items-center min-w-[100px]">
                    <span class="text-[9px] text-red-500 font-black uppercase tracking-tighter">Inoperativas</span>
                    <span class="text-xl font-black text-red-600" x-text="camarasList.filter(c => !c.operativa).length"></span>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <!-- Cálculo Dinámico de Distribución -->
                <div class="text-right hidden md:block">
                    <template x-if="selectedOperadores.length > 0">
                        <div class="flex flex-col">
                            <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Distribución estimada</span>
                            <span class="text-xs font-black text-blue-600" x-text="`~${Math.ceil(camarasList.filter(c => c.operativa).length / selectedOperadores.length)} cámaras p/o`"></span>
                        </div>
                    </template>
                    <template x-if="selectedOperadores.length === 0">
                        <span class="text-[9px] text-red-400 font-bold uppercase italic">Sin operadores</span>
                    </template>
                </div>

                <div class="relative w-64">
                    <input type="text" x-model="asignacionCamaraQuery" x-ref="camaraSearchInput" placeholder="Buscar cámara..." class="w-full bg-slate-50 border-none rounded-2xl py-2.5 pl-10 pr-9 text-sm font-bold text-slate-600 focus:ring-2 focus:ring-blue-500 transition-all">
                    <svg class="w-4 h-4 text-slate-400 absolute left-4 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <!-- Botón X para limpiar búsqueda -->
                    <button type="button"
                            x-show="asignacionCamaraQuery.length > 0"
                            @click="asignacionCamaraQuery = ''; $refs.camaraSearchInput.focus()"
                            class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <button @click="distribuirCamaras()" class="bg-blue-600 text-white font-black text-[10px] px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 uppercase tracking-widest flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                    </svg>
                    <span>Auto-Distribuir</span>
                </button>
            </div>
        </div>
        
        <!-- Grid de Cámaras -->
        <div class="flex-1 overflow-y-auto p-8 bg-slate-50/50 custom-scrollbar">
            <div x-show="cargandoCamarasCSV" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center space-y-3">
                    <svg class="w-10 h-10 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm font-bold text-slate-600">Escaneando cámaras desde el CSV...</p>
                </div>
            </div>
            
            <div x-show="!cargandoCamarasCSV && camarasList.length === 0" class="flex items-center justify-center py-12">
                <div class="flex flex-col items-center space-y-3">
                    <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm font-bold text-slate-500">No hay cámaras activas para mostrar</p>
                    <button @click="cargarCamarasDesdeCSV()" class="text-blue-600 hover:text-blue-700 text-sm font-bold underline">
                        Intentar nuevamente
                    </button>
                </div>
            </div>
            
            <div x-show="!cargandoCamarasCSV && camarasList.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="cam in camarasList.filter(c => c.name.toLowerCase().includes(asignacionCamaraQuery.toLowerCase()))" :key="cam.name">
                    <div @click="toggleCamaraStatus(cam)" 
                         :class="cam.operativa ? 'bg-white border-blue-500 ring-2 ring-blue-50' : 'bg-slate-100 border-transparent opacity-60'"
                         class="p-4 rounded-2xl border-2 transition-all cursor-pointer flex items-center justify-between group">
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <div :class="cam.operativa ? 'bg-blue-600' : 'bg-slate-400'" class="w-2 h-2 rounded-full flex-shrink-0 animate-pulse"></div>
                            <span class="text-[11px] font-bold text-slate-700 truncate uppercase" x-text="cam.name"></span>
                        </div>
                        <div :class="cam.operativa ? 'text-blue-600' : 'text-slate-400'" class="flex-shrink-0">
                            <svg x-show="cam.operativa" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="!cam.operativa" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 bg-white border-t border-slate-100 flex justify-between items-center">
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">
                <span class="text-blue-600">Info:</span> Solo se muestran cámaras ONLINE desde el CSV (excluye LPR y Control de Acceso)
            </p>
            <button @click="showCamarasAsignacionModal = false" class="bg-red-600 text-white font-black px-12 py-3 rounded-2xl hover:bg-red-700 transition-all shadow-xl uppercase text-xs tracking-widest flex items-center space-x-2">
                <span>CERRAR</span>
            </button>
        </div>
    </div>
</div>
