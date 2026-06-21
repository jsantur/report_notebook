<!-- Sección: Visualizaciones Resaltantes -->
<div class="mb-6">
    <!-- Card Header -->
    <div :class="camarasReportes.length > 0 ? 'bg-green-600' : 'bg-blue-600'" class="flex items-center justify-between text-white p-3 rounded-t-xl transition-all shadow-lg shadow-black/5">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <span class="font-semibold text-uppercase">5. Visualizaciones Resaltantes (IA)</span>
            <span x-show="camarasReportes.length > 0" class="bg-white/20 text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse" x-text="`${camarasReportes.length} GUARDADAS`"></span>
        </div>
        <!-- FAB -->
        <button type="button" 
                @click="window.dispatchEvent(new CustomEvent('abrir-modal-visualizaciones'))" 
                class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-full shadow-lg transition-all" title="Agregar Visualización">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </button>
    </div>
    
    <!-- Contenido - Lista de visualizaciones (solo si hay datos) -->
    <div x-show="camarasReportes.length > 0" class="bg-white border border-gray-100 rounded-b-xl p-4 max-h-64 overflow-y-auto scrollbar-thin">
        <template x-for="rep in camarasReportes" :key="rep.id">
            <div @click="window.dispatchEvent(new CustomEvent('abrir-modal-visualizaciones', { detail: rep }))" class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex justify-between items-start group mb-3 last:mb-0 cursor-pointer hover:bg-gray-100 transition-colors">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded" x-text="rep.hora"></span>
                        <span class="text-[10px] text-slate-700 font-black uppercase" x-text="rep.camara"></span>
                        <span x-show="rep.is_ai" class="text-[10px] bg-blue-500 text-white px-2 py-0.5 rounded font-bold">IA</span>
                    </div>
                    <p :class="rep.is_ai ? 'text-blue-600 font-bold' : 'text-gray-600 font-medium'" class="text-xs italic leading-relaxed" x-text="rep.corregido || rep.original"></p>
                </div>
                <button type="button" @click.stop="removeCamaraReport(rep.id)" class="opacity-0 group-hover:opacity-100 p-1 text-red-400 hover:text-red-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </template>
    </div>
    
    <!-- Input oculto para visualizaciones -->
    <input type="hidden" name="visualizaciones_resaltantes" value="">
</div>
