<div 
    x-data="{ 
        show: false,
        search: '',
        list: [],
        selectedIds: [],
        
        init() {
                // Sincronizar con el estado global de seleccionados al abrir
                this.$watch('show', (value) => {
                    if (value) {
                        // Scroll to top
                        this.$nextTick(() => {
                            const modalContainer = this.$el;
                            if (modalContainer) {
                                modalContainer.scrollTop = 0;
                            }
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                        
                        this.syncSelected();
                        this.fetch();
                    }
                });
            },

        syncSelected() {
            try {
                // Obtener los IDs ya seleccionados del componente principal usando Alpine.$data
                const mainEl = document.getElementById('reporte-main');
                if (mainEl) {
                    const mainData = Alpine.$data(mainEl);
                    this.selectedIds = mainData.selectedOperadores.map(s => s.id);
                }
            } catch (e) {
                console.error('Error sincronizando seleccionados:', e);
            }
        },

        async fetch() {
            const response = await fetch(`{{ route('api.serenazgo.search') }}?role=Operador de Cámaras&search=${this.search}&activo=1&json=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            this.list = data.map(op => ({
                ...op,
                nombres: op.nombres.toUpperCase(),
                apellido_paterno: op.apellido_paterno.toUpperCase(),
                apellido_materno: op.apellido_materno ? op.apellido_materno.toUpperCase() : ''
            })).sort((a, b) => a.nombres.localeCompare(b.nombres)); // Orden alfabético por nombres
        },

        toggle(op) {
            // Notificar al componente principal
            window.dispatchEvent(new CustomEvent('toggle-operador', { detail: op }));
            
            // Actualizar estado local para feedback visual inmediato
            if (this.selectedIds.includes(op.id)) {
                this.selectedIds = this.selectedIds.filter(id => id !== op.id);
            } else {
                this.selectedIds.push(op.id);
            }
        },

        isSelected(id) {
            return this.selectedIds.includes(id);
        }
    }"
    @abrir-modal-operadores.window="show = true; search = ''; $nextTick(() => { syncSelected(); fetch(); $refs.searchInput.focus(); })"
    @keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    style="display: none;"
    class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 sm:pt-20 overflow-y-auto"
>
    <!-- Backdrop -->
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>

    <!-- Modal Content -->
    <div 
        x-show="show" 
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-4xl transform overflow-hidden rounded-[32px] bg-white shadow-2xl transition-all"
        @click.stop
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-2.5 rounded-2xl text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white">Selección de Operadores</h3>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest opacity-80">Gestión de Personal de Cámaras</p>
                        <p class="text-[9px] text-yellow-300 font-bold mt-1 flex items-center uppercase tracking-wider">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Presiona ESC para cerrar la ventana
                        </p>
                        <p class="text-[9px] text-green-300 font-bold mt-1 flex flex-wrap items-center uppercase tracking-wider">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tip: El orden en que selecciones a los operadores definirá quién recibe las primeras cámaras al auto-distribuir.
                        </p>
                    </div>
                </div>
                <button @click="show = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-8">
            <!-- Search Bar -->
            <div class="relative mb-8">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input 
                    x-ref="searchInput"
                    type="text" 
                    x-model="search" 
                    @input="search = search.replace(/[0-9]/g, '')"
                    @input.debounce.300ms="fetch()" 
                    placeholder="Buscar por nombre o apellido..." 
                    class="block w-full pl-12 pr-12 py-4 bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 text-sm font-bold text-slate-600 transition-all shadow-inner"
                >
                <!-- Botón Limpiar (X) -->
                <button 
                    type="button" 
                    x-show="search.length > 0" 
                    @click="search = ''; fetch(); $refs.searchInput.focus();"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Operators Grid (Compact & Horizontal Layout) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                <template x-for="op in list" :key="op.id">
                    <div 
                        @click="toggle(op)"
                        :class="isSelected(op.id) ? 'bg-blue-600 border-blue-600 shadow-blue-100 ring-4 ring-blue-50' : 'bg-white border-slate-100 hover:border-blue-200 hover:bg-slate-50 shadow-sm'"
                        class="p-3 border-2 rounded-2xl cursor-pointer transition-all flex items-center justify-between group overflow-hidden"
                    >
                        <div class="flex items-center space-x-3 overflow-hidden">
                            <!-- Avatar/Initial Circle -->
                            <div 
                                :class="isSelected(op.id) ? 'bg-white text-blue-600' : 'bg-slate-100 text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-500'"
                                class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-sm transition-all flex-shrink-0"
                                x-text="op.nombres[0]"
                            ></div>
                            <div class="overflow-hidden">
                                <h4 
                                    :class="isSelected(op.id) ? 'text-white' : 'text-slate-800'"
                                    class="font-black text-[11px] uppercase tracking-tighter truncate" 
                                    x-text="`${op.nombres} ${op.apellido_paterno}`"
                                ></h4>
                                <p 
                                    :class="isSelected(op.id) ? 'text-blue-100' : 'text-slate-400'"
                                    class="text-[9px] font-bold uppercase truncate" 
                                    x-text="op.perfil_trabajo || 'OPERADOR'"
                                ></p>
                            </div>
                        </div>
                        
                        <!-- Status Icon -->
                        <div 
                            :class="isSelected(op.id) ? 'bg-white/20' : 'bg-slate-50 group-hover:bg-blue-100'"
                            class="w-6 h-6 rounded-lg flex items-center justify-center transition-all flex-shrink-0"
                        >
                            <svg x-show="isSelected(op.id)" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg x-show="!isSelected(op.id)" class="w-4 h-4 text-slate-300 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                    </div>
                </template>
                
                <div x-show="list.length === 0" class="col-span-full py-12 text-center">
                    <div class="bg-slate-50 w-16 h-16 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-slate-400 font-bold text-sm">No se encontraron operadores</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 px-8 py-5 flex justify-end items-center border-t border-slate-100">
            <p class="mr-auto text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                <span class="text-blue-600" x-text="selectedIds.length"></span> Operadores seleccionados
            </p>
            <button 
                type="button" 
                @click="show = false"
                class="inline-flex items-center rounded-2xl border-2 border-orange-100 bg-white px-8 py-3 text-xs font-black text-orange-600 shadow-sm hover:bg-orange-50 hover:border-orange-300 transition-all space-x-2 uppercase tracking-widest"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>CERRAR</span>
            </button>
        </div>
    </div>
</div>
