<!-- MODAL: Ver Lista Completa - Personal de Campo -->
<div x-show="showCampoListaCompletaModal" 
     @keydown.escape.window="showCampoListaCompletaModal = false"
     @click="showCampoListaCompletaModal = false"
     class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 sm:pt-16 bg-black/50 backdrop-blur-sm overflow-y-auto" 
     x-cloak 
     x-transition>
    
    <div class="bg-white w-full max-w-6xl rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]" @click.stop>
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-3 rounded-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold">Distribuci&oacute;n Completa del Personal de Campo</h2>
                    <p class="text-blue-200 text-sm" x-text="`${selectedCampo.length} registros cargados`"></p>
                </div>
            </div>
            <button @click="showCampoListaCompletaModal = false" class="text-white/80 hover:text-white transition-colors p-2 hover:bg-white/20 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Tipo / Descripci&oacute;n</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Ubicaci&oacute;n</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Unidad / Personal</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Celular</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-600 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="p in sortedCampoLista" :key="p.id">
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800" x-text="p.tipo_patrullaje"></span>
                                        <span class="text-xs text-gray-500 uppercase" x-text="p.descripcion"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-700" x-text="p.ubicacion"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <template x-if="p.tipo_patrullaje === 'Vehicular'">
                                        <div class="flex flex-col text-xs">
                                            <span class="font-bold text-blue-600" x-text="`${p.unidad} (${p.matricula})`"></span>
                                            <span class="text-gray-600" x-text="`C: ${p.chofer || 'N/A'}`"></span>
                                            <span class="text-gray-600" x-text="`O: ${p.operador || 'N/A'}`"></span>
                                            <span x-show="p.lince" class="text-gray-600" x-text="`L: ${p.lince}`"></span>
                                            <div x-show="p.patrullaje_integrado && p.patrullaje_integrado.length > 0" class="mt-1 text-xs text-green-600">
                                                <span x-text="`P. Integrado (${p.patrullaje_integrado.length}): ${p.patrullaje_integrado.map(i => i.apellidos).join(', ')}`"></span>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="p.tipo_patrullaje === 'Motorizado'">
                                        <div class="flex flex-col text-xs">
                                            <span class="font-bold text-orange-600" x-text="p.unidad"></span>
                                            <span class="text-gray-600" x-text="`Chofer: ${p.chofer || 'N/A'}`"></span>
                                        </div>
                                    </template>
                                    <template x-if="['A pie', 'Prevenci&oacute;n', 'Cecom'].includes(p.tipo_patrullaje)">
                                        <div class="flex flex-col text-xs">
                                            <span class="font-bold text-slate-700" 
                                                  x-text="p.tipo_patrullaje === 'A pie' ? `SBR ${p.cantidad}` : 
                                                         (p.tipo_patrullaje === 'Cecom' ? `CECOM: ${p.cantidad}` : 
                                                         (p.tipo_patrullaje === 'Prevenci&oacute;n' ? `PREV: ${p.cantidad}` : p.cantidad))"></span>
                                            <span class="text-gray-600" x-text="`Sereno: ${p.sereno || 'N/A'}`"></span>
                                        </div>
                                    </template>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-700" x-text="p.celular || '-'"></span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-center">
                                        <button type="button" @click="removeCampo(p.id)" class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-xl transition-all" title="Eliminar">
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
                <div x-show="selectedCampo.length === 0" class="text-center py-12 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>No hay registros de personal de campo.</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 border-t border-gray-200 flex justify-between items-center">
            <span class="text-sm text-gray-500" x-text="`Total: ${selectedCampo.length} registros`"></span>
            <button @click="showCampoListaCompletaModal = false" class="inline-flex items-center rounded-lg border-2 border-orange-100 bg-white px-8 py-2.5 text-sm font-bold text-orange-600 shadow-sm hover:bg-orange-50 hover:border-orange-300 transition-all uppercase tracking-widest">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                CERRAR
            </button>
        </div>
    </div>
</div>
