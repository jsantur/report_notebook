<!-- Sección: Ocurrencias del Relevo -->
<div class="mb-6">
    <!-- Card Header -->
    <div :class="isSaved ? 'bg-green-600' : 'bg-blue-600'" class="flex items-center justify-between text-white p-3 rounded-t-xl transition-all shadow-lg shadow-black/5">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="font-semibold">Ocurrencias del Relevo del personal de cámaras</span>
            <span x-show="isSaved" class="bg-white/20 text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse">1 GUARDADA</span>
        </div>
        <!-- FAB -->
        <button type="button" @click="$dispatch('abrir-modal-ocurrencia')" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-full shadow-lg transition-all" title="Agregar/Editar">
            <svg x-show="!isSaved" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <svg x-show="isSaved" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
        </button>
    </div>
    
    <!-- Contenido -->
    <div class="bg-white border border-gray-100 rounded-b-xl p-4">
        <div x-show="!isSaved" class="text-center py-4 text-gray-500 text-sm">
            No hay ocurrencias registradas. Haz clic en el bot&oacute;n + para agregar.
        </div>
        
        <div x-show="isSaved && incidenciaData.hora">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-4 mb-2">
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">
                            <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span x-text="incidenciaData.hora"></span>
                        </span>
                    </div>
                    <div class="text-sm text-gray-800 bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <span x-text="incidenciaData.detalles"></span>
                    </div>
                </div>
                <div class="flex space-x-1 ml-4">
                    <button type="button" @click="$dispatch('editar-ocurrencia', {hora: incidenciaData.hora, detalle: incidenciaData.detalles})" class="p-1 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                    <button type="button" @click="eliminarOcurrencia()" class="p-1 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <input type="hidden" name="ocurrencias_relevo_hora" :value="incidenciaData.hora">
    <textarea name="ocurrencias_relevo" class="hidden" :value="incidenciaData.hora ? '[' + incidenciaData.hora + '] ' + incidenciaData.detalles : incidenciaData.detalles"></textarea>
</div>
