<!-- Sección: Distribución de Personal de Cámaras -->
<div class="mb-6">
    <!-- Card Header -->
    <div :class="selectedOperadores.length > 0 ? 'bg-green-600' : 'bg-blue-600'" class="flex items-center justify-between text-white p-3 rounded-t-xl transition-all shadow-lg shadow-black/5">
        <div class="flex items-center space-x-3">
            <div class="bg-white/20 p-2 rounded-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <span class="font-semibold">Distribuci&oacute;n de Personal de C&aacute;maras</span>
            <span x-show="selectedOperadores.length > 0" class="bg-white/20 text-[10px] px-2 py-0.5 rounded-full font-bold animate-pulse" x-text="`${selectedOperadores.length} SELECCIONADOS`"></span>
        </div>
        <!-- FABs -->
        <div class="flex items-center space-x-2">
            <button type="button" @click="showCamarasAsignacionModal = true" 
                    :class="selectedOperadores.length > 0 ? 'bg-green-500 hover:bg-green-700 shadow-green-200' : 'bg-blue-500 hover:bg-blue-700 shadow-blue-200'"
                    class="text-white text-xs font-semibold px-3 py-1.5 rounded-full shadow-md transition-all flex items-center space-x-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span>Gestionar Cámaras</span>
            </button>
            <button type="button" @click="window.dispatchEvent(new CustomEvent('abrir-modal-operadores'))" class="bg-white/20 hover:bg-white/30 text-white p-2 rounded-full shadow-lg transition-all" title="Agregar/Editar Operadores">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Contenido - Tabla (solo si hay datos) -->
    <div x-show="selectedOperadores.length > 0" class="bg-white border border-gray-100 rounded-b-xl overflow-hidden max-h-64 overflow-y-auto scrollbar-thin relative">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Operador</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Puesto</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase">Máquina</th>
                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="op in selectedOperadores" :key="op.id">
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-800 uppercase" x-text="`${op.nombres} ${op.apellido_paterno}`"></span>
                                <div class="mt-1 flex flex-wrap gap-1">
                                    <template x-if="op.camaras && op.camaras.length > 0">
                                        <div class="flex flex-wrap gap-1">
                                            <span class="text-[9px] bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded font-black uppercase" x-text="`${op.camaras.length} CÁMARAS`"></span>
                                            <button type="button" 
                                                    @click="Swal.fire({ 
                                                        title: '📌 Cámaras Asignadas - ' + op.nombres, 
                                                        html: `
                                                            <div class='bg-slate-50 p-4 rounded-2xl border border-slate-100'>
                                                                <div class='flex justify-between items-center mb-4 pb-2 border-b border-slate-200'>
                                                                    <span class='text-[10px] font-black text-slate-400 uppercase tracking-widest'>Lista de Dispositivos</span>
                                                                    <span class='bg-blue-600 text-white text-[10px] px-2 py-0.5 rounded-full font-bold'>Total: ${op.camaras.length}</span>
                                                                </div>
                                                                <div class='text-left grid grid-cols-1 gap-2 max-h-80 overflow-y-auto pr-2 custom-scrollbar'>
                                                                    ${op.camaras.map((c, i) => `
                                                                        <div class='flex items-center space-x-3 bg-white p-2 rounded-xl border border-slate-100 shadow-sm'>
                                                                            <span class='bg-slate-100 text-slate-500 text-[9px] font-black w-5 h-5 flex items-center justify-center rounded-lg'>${String(i + 1).padStart(2, '0')}</span>
                                                                            <span class='text-xs font-bold text-slate-700 uppercase'>${c}</span>
                                                                        </div>
                                                                    `).join('')}
                                                                </div>
                                                            </div>
                                                        `,
                                                        customClass: { 
                                                            popup: 'rounded-[32px] border-none shadow-2xl',
                                                            confirmButton: 'bg-blue-600 rounded-xl px-10 font-bold uppercase text-xs tracking-widest'
                                                        } 
                                                    })" 
                                                    class="text-[9px] text-blue-500 hover:text-blue-700 font-black flex items-center space-x-1 bg-blue-50 px-2 py-0.5 rounded-full transition-all">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <span>VER LISTA</span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!op.camaras || op.camaras.length === 0">
                                        <span class="text-[9px] text-gray-300 font-bold italic uppercase tracking-tighter">Sin cámaras asignadas</span>
                                    </template>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500" x-text="op.perfil_trabajo"></td>
                        <td class="px-6 py-4">
                            <select x-model="op.maquina" 
                                    @change="saveDraft()" 
                                    :class="!op.maquina ? 'text-red-500 border-red-200 animate-pulse' : 'text-blue-600 border-gray-200'"
                                    class="text-sm bg-transparent focus:outline-none border-b font-bold cursor-pointer transition-all">
                                <option value="" disabled :selected="!op.maquina">-- ASIGNAR MÁQUINA --</option>
                                <template x-for="m in maquinas" :key="m">
                                    <option :value="m" 
                                            x-text="m" 
                                            :selected="op.maquina === m"
                                            :disabled="isMaquinaOcupada(m, op.id)"
                                            :class="isMaquinaOcupada(m, op.id) ? 'text-gray-300' : 'text-gray-800'"></option>
                                </template>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="showOperadoresModal = true; fetchOperadores()" class="text-green-500 hover:text-green-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                    </svg>
                                </button>
                                <button type="button" @click="removeOperador(op.id)" class="text-red-500 hover:text-red-700">
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
    
    <!-- Input oculto con el JSON de operadores -->
    <input type="hidden" name="operadores_camaras" value="">
</div>
