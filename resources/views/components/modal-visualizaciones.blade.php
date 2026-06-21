<div 
    x-data="{ 
        show: false,
        isCorrectingAI: false,
        tempCamara: { nombre: '', hora: '', descripcion: '', descripcion_corregida: '' },
        camaraQuery: '',
        camaraFiltered: [],
        camaraIndex: -1,
        editingId: null,

        init() {
            this.$watch('show', (value) => {
                if (value) {
                    // Scroll modal to top
                    this.$nextTick(() => {
                        const modalContainer = this.$el;
                        if (modalContainer) {
                            modalContainer.scrollTop = 0;
                        }
                        // Also scroll page to top
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                    
                    if (!this.editingId) {
                        const now = new Date();
                        this.tempCamara.hora = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    }
                }
            });
        },

        async fetchAI(text) {
            const response = await fetch('{{ route('ai.correct') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ text: text })
            });
            return await response.json();
        },

        async save() {
            if (!this.tempCamara.descripcion) {
                Swal.fire('Atención', 'Por favor completa la descripción.', 'warning');
                return;
            }

            this.isCorrectingAI = true;
            try {
                const data = await this.fetchAI(this.tempCamara.descripcion);
                const isAiCorrected = data.is_ai_corrected;
                
                const reportData = {
                    id: this.editingId || Date.now(),
                    camara: this.tempCamara.nombre.toUpperCase(),
                    hora: this.tempCamara.hora || new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
                    original: this.tempCamara.descripcion,
                    corregido: data.corrected_text,
                    is_ai: isAiCorrected
                };

                window.dispatchEvent(new CustomEvent('visualizacion-saved', { 
                    detail: { report: reportData, isEdit: !!this.editingId } 
                }));

                if (!isAiCorrected) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Corrección local aplicada',
                        text: 'El texto fue corregido con reglas básicas. La IA no está disponible en este momento.',
                        confirmButtonColor: '#2563eb'
                    });
                }

                this.close();
            } catch (error) {
                console.error('Error IA:', error);
                Swal.fire('Error', 'No se pudo conectar con la IA', 'error');
            } finally {
                this.isCorrectingAI = false;
            }
        },

        filterCamaras() {
            if (this.camaraQuery.length < 2) {
                this.camaraFiltered = [];
                return;
            }
            const q = this.camaraQuery.trim().toLowerCase();
            const mainData = Alpine.$data(document.getElementById('reporte-main'));
            const results = mainData.camarasList.filter(c => c.operativa && c.name.toLowerCase().includes(q)).map(c => c.name);
            
            if (results.length === 1 && results[0].toLowerCase() === q) {
                this.camaraFiltered = [];
            } else {
                this.camaraFiltered = results.slice(0, 5);
            }
        },

        selectCamara(name) {
            this.tempCamara.nombre = name;
            this.camaraQuery = name;
            this.camaraFiltered = [];
            this.camaraIndex = -1;
        },

        open(data = null) {
            if (data) {
                this.editingId = data.id;
                this.tempCamara = {
                    nombre: data.camara,
                    hora: data.hora,
                    descripcion: data.corregido || data.original,
                    descripcion_corregida: data.corregido || ''
                };
                this.camaraQuery = data.camara;
            } else {
                this.editingId = null;
                this.tempCamara = { nombre: '', hora: '', descripcion: '', descripcion_corregida: '' };
                this.camaraQuery = '';
            }
            this.show = true;
        },

        close() {
            this.show = false;
            this.editingId = null;
        }
    }"
    @abrir-modal-visualizaciones.window="open($event.detail)"
    @keydown.escape.window="close()"
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
        class="relative w-full max-w-4xl transform overflow-hidden rounded-[32px] bg-[#f8fafc] shadow-2xl transition-all"
        @click.stop
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative flex-shrink-0 w-20 h-16 flex items-center justify-center">
                        <img src="{{ asset('img/logo_ocurrencia.png') }}" alt="Logo" class="absolute w-[150%] h-[250%] object-contain -top-12 -left-2 drop-shadow-lg">
                    </div>
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white">Visualizaciones IA</h3>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest opacity-80">Reporte de Ocurrencias con Inteligencia Artificial</p>
                        <p class="text-[9px] text-yellow-300 font-bold mt-1 flex items-center uppercase tracking-wider">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Presiona ESC para cerrar la ventana
                        </p>
                    </div>
                </div>
                <button @click="close()" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <!-- Status Bar -->
            <div class="flex items-center space-x-6 text-[10px] font-black uppercase tracking-widest">
                <div class="flex items-center text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    IA: Operativa
                </div>
                <div class="flex items-center text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                    <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Motor: Gemini 1.5 Flash
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white border border-slate-100 rounded-[24px] p-6 shadow-sm space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Cámara Input -->
                    <div class="relative">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cámara Seleccionada</label>
                        <div class="relative">
                            <input 
                                type="text" 
                                x-model="camaraQuery"
                                @input="filterCamaras()"
                                @keydown.down.prevent="if(camaraFiltered.length > 0) camaraIndex = (camaraIndex + 1) % camaraFiltered.length"
                                @keydown.up.prevent="if(camaraFiltered.length > 0) camaraIndex = (camaraIndex - 1 + camaraFiltered.length) % camaraFiltered.length"
                                @keydown.enter.prevent="if(camaraIndex >= 0) { selectCamara(camaraFiltered[camaraIndex]); } else if(camaraQuery.length > 0) { selectCamara(camaraQuery); }"
                                class="w-full bg-slate-50 border-none rounded-2xl py-3.5 pl-4 pr-10 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all uppercase"
                                placeholder="BUSCAR CÁMARA..."
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                        </div>

                        <!-- Autocompletado -->
                        <div x-show="camaraFiltered.length > 0" 
                             class="absolute left-0 right-0 z-50 mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl overflow-hidden ring-4 ring-slate-50"
                             @click.away="camaraFiltered = []">
                            <template x-for="(c, index) in camaraFiltered" :key="c">
                                <div @click="selectCamara(c)" 
                                     :class="{ 'bg-blue-600 text-white': camaraIndex === index, 'text-slate-600': camaraIndex !== index }"
                                     class="px-4 py-3 hover:bg-blue-600 hover:text-white cursor-pointer text-xs font-bold uppercase transition-colors"
                                     x-text="c"></div>
                            </template>
                        </div>
                    </div>

                    <!-- Hora Input -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Hora del Suceso</label>
                        <input 
                            type="time" 
                            x-model="tempCamara.hora"
                            class="w-full bg-slate-50 border-none rounded-2xl py-3.5 px-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all"
                        >
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Descripción de la Ocurrencia</label>
                    <textarea 
                        x-model="tempCamara.descripcion"
                        rows="6"
                        class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-blue-500 transition-all resize-none custom-scrollbar"
                        placeholder="Describa lo visualizado por las cámaras..."
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 px-8 py-5 flex justify-end items-center space-x-3 border-t border-slate-100">
            <button 
                type="button" 
                @click="close()"
                class="inline-flex items-center rounded-2xl border-2 border-orange-100 bg-white px-8 py-3 text-xs font-black text-orange-600 shadow-sm hover:bg-orange-50 hover:border-orange-300 transition-all space-x-2 uppercase tracking-widest"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Cancelar</span>
            </button>

            <button 
                type="button" 
                @click="save()"
                :disabled="isCorrectingAI"
                class="inline-flex items-center rounded-2xl bg-blue-600 px-10 py-3 text-xs font-black text-white shadow-lg shadow-blue-100 hover:bg-blue-700 transition-all space-x-2 uppercase tracking-widest disabled:opacity-50"
            >
                <template x-if="!isCorrectingAI">
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                        </svg>
                        <span>Guardar</span>
                    </div>
                </template>
                <template x-if="isCorrectingAI">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Procesando IA...</span>
                    </div>
                </template>
            </button>
        </div>
    </div>
</div>
