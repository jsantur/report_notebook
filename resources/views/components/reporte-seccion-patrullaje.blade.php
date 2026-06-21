<!-- Sección: Reporte Personal Patrullando -->
<div class="mb-6">
    <!-- Card Header Dinámico -->
    <div :class="reportesUnidades.length > 0 ? 'bg-green-600' : (patrullajeAutomatico.length > 0 ? 'bg-blue-600' : 'bg-gray-400')" 
         class="flex items-center justify-between text-white p-3 rounded-t-xl transition-all shadow-lg shadow-black/5">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <span class="font-semibold text-uppercase">4. Reporte de Patrullaje (Unidades)</span>
            <span x-show="reportesUnidades.length > 0" class="bg-white/20 text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse" x-text="`${reportesUnidades.length} REPORTES`"></span>
        </div>
        <!-- FAB -->
        <button type="button" 
                @click="window.dispatchEvent(new CustomEvent('abrir-modal-unidades'))" 
                :class="patrullajeAutomatico.length > 0 ? 'bg-white/20 hover:bg-white/30' : 'bg-gray-400 cursor-not-allowed'"
                :disabled="patrullajeAutomatico.length === 0"
                class="text-white p-2 rounded-full shadow-lg transition-all" title="Agregar Reporte">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>
    </div>
    
    <!-- Contenido - Lista de reportes (solo si hay datos) -->
    <div x-show="reportesUnidades.length > 0" class="bg-white border border-gray-100 rounded-b-xl p-4 max-h-64 overflow-y-auto scrollbar-thin">
        <template x-for="rep in reportesUnidades" :key="rep.id">
            <div @click="editarReporteUnidades(rep)" 
                 class="bg-slate-50 hover:bg-slate-100 rounded-2xl p-4 border border-slate-200 flex justify-between items-center group cursor-pointer transition-all shadow-sm mb-3">
                <div class="flex items-center space-x-4">
                    <div class="bg-white p-2.5 rounded-xl shadow-sm text-slate-400 group-hover:text-blue-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-700">
                            Reporte guardado a las <span x-text="rep.hora" class="text-blue-600"></span>
                        </h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5" 
                           x-text="`${rep.unidadesCount} Serenos reportados`"></p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" @click.stop="eliminarReporteUnidades(rep.id)" class="p-2 text-red-400 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    <div class="text-slate-300 group-hover:text-blue-500 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </template>
    </div>
    
    <!-- Input oculto — se rellena en clearDraft() antes del POST -->
    <input type="hidden" name="reporte_personal_patrullando" value="">
</div>
