<!-- Sección: Distribución Personal Campo -->
<div class="mb-6">
    <!-- Card Header -->
    <div :class="selectedCampo.length > 0 ? 'bg-green-600' : 'bg-blue-600'" class="flex items-center justify-between text-white p-3 rounded-t-xl transition-all shadow-lg shadow-black/5">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <span class="font-semibold text-uppercase">3. Distribución del Personal de Campo</span>
            <span x-show="selectedCampo.length > 0" class="bg-white/20 text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse" x-text="`${selectedCampo.length} ASIGNADOS`"></span>
        </div>
        <!-- FABs -->
        <div class="flex items-center space-x-2">
            <button type="button" @click="showCampoListaCompletaModal = true" 
                    :class="selectedCampo.length > 0 ? 'bg-green-500 hover:bg-green-700 shadow-green-200' : 'bg-blue-500 hover:bg-blue-700 shadow-blue-200'"
                    class="text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-md transition-all flex items-center space-x-1 uppercase tracking-tighter">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <span>Ver lista completa</span>
            </button>
            <button type="button" @click="$dispatch('abrir-modal-campo')" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-full shadow-lg transition-all" title="Agregar/Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Contenido - Tabla (solo si hay datos) -->
    <div x-show="selectedCampo.length > 0" class="bg-white border border-gray-100 rounded-b-xl overflow-hidden max-h-64 overflow-y-auto scrollbar-thin relative">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Patrullaje / Descripci&oacute;n</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Ubicaci&oacute;n / Celular</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="p in selectedCampo" :key="p.id">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800" x-text="p.tipo_patrullaje"></span>
                                <span class="text-xs text-gray-400 font-medium uppercase" x-text="p.descripcion"></span>
                                <template x-if="['A pie', 'Prevenci&oacute;n', 'Cecom'].includes(p.tipo_patrullaje)">
                                    <div class="mt-1 text-[10px] text-green-600 font-bold uppercase">
                                        <span x-text="`${p.tipo_patrullaje === 'A pie' ? 'SBR' : (p.tipo_patrullaje === 'Cecom' ? 'CECOM' : 'PREV')}: ${p.cantidad} | Sereno: ${p.sereno}`"></span>
                                    </div>
                                </template>
                                <template x-if="p.tipo_patrullaje === 'Vehicular'">
                                    <div class="mt-1 text-[10px] text-blue-600 font-bold uppercase">
                                        <span x-text="`Unidad: ${p.unidad} | Plate: ${p.matricula}`"></span><br>
                                        <span x-text="`C: ${p.chofer} | O: ${p.operador}${p.lince ? ' | L: '+p.lince : ''}`"></span>
                                        <template x-if="p.patrullaje_integrado && p.patrullaje_integrado.length > 0">
                                            <div class="text-gray-500 mt-1">
                                                <span x-text="`P. Integrado (${p.patrullaje_integrado.length}): ${p.patrullaje_integrado.map(i => i.apellidos).join(', ')}`"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-blue-600" x-text="p.ubicacion"></span>
                                <span class="text-xs text-gray-500" x-text="p.celular"></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="$dispatch('abrir-modal-campo', p)" class="text-green-500 hover:text-green-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button" @click="removeCampo(p.id)" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
    
    <!-- Input oculto con el JSON de personal de campo -->
    <input type="hidden" name="personal_campo" value="">
</div>
