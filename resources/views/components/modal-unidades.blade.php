<div 
    x-data="{ 
        show: false,
        horaReporte: '',
        patrullajeAutomatico: [],
        editingReportId: null,

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
                    
                    this.syncData();
                }
            });
        },

        syncData() {
            try {
                const mainEl = document.getElementById('reporte-main');
                if (mainEl) {
                    const mainData = Alpine.$data(mainEl);
                    
                    if (!this.editingReportId) {
                        this.patrullajeAutomatico = JSON.parse(JSON.stringify(mainData.patrullajeAutomatico));
                        const now = new Date();
                        this.horaReporte = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                    }
                }
            } catch (e) {
                console.error('Error sincronizando datos de unidades:', e);
            }
        },

        close() {
            this.show = false;
            this.editingReportId = null;
            this.horaReporte = '';
        },

        patrullajeItems(grupo) {
            const items = this.patrullajeAutomatico.filter(p => p.grupo === grupo);
            const num = (v) => {
                const n = parseInt(v, 10);
                return Number.isFinite(n) ? n : 9999;
            };
            return items.sort((a, b) => num(a.unidad) - num(b.unidad));
        },

        async generateAndCopyReport() {
            const hora = this.horaReporte || (() => {
                const now = new Date();
                return `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
            })();
            
            const order = { 'HALCON': 1, 'CAZADOR': 2, 'SIERRA_BRAVO': 3 };
            const seleccionadas = this.patrullajeAutomatico
                .filter(p => p.incluido)
                .sort((a, b) => (order[a.grupo] || 99) - (order[b.grupo] || 99));

            // Validación: cada unidad seleccionada debe tener observación escrita
            for (const p of seleccionadas) {
                if (!p.observacion || !p.observacion.trim()) {
                    Swal.fire('Atención', `Complete la observación para la unidad ${p.display}.`, 'warning');
                    return;
                }
            }
            
            if (seleccionadas.length === 0) {
                Swal.fire('Atención', 'Por favor selecciona al menos una unidad.', 'warning');
                return;
            }

            let reportText = `*REPORTE DE UNIDADES - ${hora}*\n\n`;
            let lastGrupo = null;
            const titulo = (g) => {
                if (g === 'HALCON') return '*HALCONES*';
                if (g === 'CAZADOR') return '*CAZADORES*';
                if (g === 'SIERRA_BRAVO') return '*SIERRA BRAVO*';
                return '*UNIDADES*';
            };

            seleccionadas.forEach(p => {
                const obs = (p.observacion || '').trim() || 'SIN NOVEDAD';
                if (p.grupo !== lastGrupo) {
                    if (lastGrupo !== null) reportText += `\n`;
                    reportText += `${titulo(p.grupo)}\n`;
                    lastGrupo = p.grupo;
                }
                reportText += `${p.display}: ${obs.toUpperCase()}\n`;
            });

            const reportObj = {
                id: this.editingReportId || Date.now(),
                hora: hora,
                detalles: reportText,
                unidadesCount: seleccionadas.length,
                rawData: JSON.parse(JSON.stringify(this.patrullajeAutomatico))
            };

            // Copiar al portapapeles
            try {
                await navigator.clipboard.writeText(reportText);
            } catch (err) {
                console.error('Error al copiar:', err);
            }

            // Notificar al componente principal
            try {
                window.dispatchEvent(new CustomEvent('unidades-report-saved', { 
                    detail: {
                        report: reportObj,
                        isEdit: !!this.editingReportId
                    }
                }));
            } catch(e) {
                Swal.fire('Error dispatch', e.message, 'error');
            }

            this.close();
        }
    }"
    @abrir-modal-unidades.window="
        if ($event.detail && $event.detail.id) {
            editingReportId = $event.detail.id;
            horaReporte = $event.detail.hora;
            patrullajeAutomatico = JSON.parse(JSON.stringify($event.detail.rawData));
            show = true;
        } else {
            editingReportId = null;
            show = true;
        }
    "
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
                    <div class="relative flex-shrink-0 w-24 h-16 flex items-center justify-center">
                        <img src="{{ asset('img/logo_reporteUnidades.png') }}" alt="Logo" class="absolute w-[180%] h-[300%] object-contain -top-16 -left-3 drop-shadow-lg">
                    </div>
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white">Reporte de Patrullaje</h3>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest opacity-80">Gestión y Novedades de Unidades en Campo</p>
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

        <div class="p-8 overflow-y-auto custom-scrollbar space-y-6 bg-slate-50 max-h-[70vh]">
            
            <!-- Información Temporal -->
            <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Información Temporal del Reporte
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Hora:</span>
                        <input type="time" x-model="horaReporte" class="bg-white border border-blue-200 rounded-lg px-3 py-1.5 text-sm font-black text-slate-700 w-full focus:outline-none focus:ring-2 focus:ring-blue-400 cursor-pointer transition-all">
                    </div>
                    <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Fecha:</span>
                        <div class="text-sm font-black text-slate-600 w-full">{{ date('Y-m-d') }}</div>
                    </div>
                    <div class="flex items-center space-x-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Turno:</span>
                        <div class="text-sm font-black text-blue-600 w-full uppercase" x-text="Alpine.$data(document.getElementById('reporte-main')).turno"></div>
                    </div>
                </div>
            </div>

            <!-- Unidades a Reportar -->
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                        Unidades Activas en Campo
                    </h3>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        
                        <!-- Categorías de Unidades -->
                        <!-- Categorías de Unidades Organizas (Grid para Halcones y Cazadores) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <template x-for="grupo in ['HALCON', 'CAZADOR']" :key="grupo">
                                <div class="bg-slate-50/50 border border-slate-100 rounded-2xl overflow-hidden">
                                    <div class="px-4 py-3 bg-white border-b border-slate-100 flex justify-between items-center">
                                        <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest" x-text="grupo === 'HALCON' ? 'HALCONES' : 'CAZADORES'"></div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase">Todo</span>
                                            <input type="checkbox" 
                                                   :checked="patrullajeItems(grupo).length > 0 && patrullajeItems(grupo).every(p => p.incluido)"
                                                   @change="const val = $event.target.checked; patrullajeItems(grupo).forEach(p => p.incluido = val)"
                                                   class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                                        </div>
                                    </div>
                                    <div class="p-4 space-y-3">
                                        <template x-for="p in patrullajeItems(grupo)" :key="p.id_campo">
                                            <div class="flex items-center space-x-4 bg-white p-3 rounded-xl border border-slate-50 shadow-sm transition-all hover:border-blue-100 group">
                                                <input type="checkbox" x-model="p.incluido" class="w-5 h-5 text-blue-600 border-slate-200 rounded-lg focus:ring-blue-500 cursor-pointer">
                                                <div class="w-32 flex-shrink-0">
                                                    <span class="text-xs font-black text-slate-700 uppercase" x-text="p.display"></span>
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text"
                                                           x-model="p.observacion"
                                                           class="w-full bg-slate-50 border border-transparent rounded-lg px-4 py-2 text-xs font-bold text-slate-600 focus:bg-white focus:border-blue-400 focus:ring-0 outline-none uppercase placeholder-slate-300 transition-all"
                                                           placeholder="SIN NOVEDAD">
                                                </div>
                                            </div>
                                        </template>
                                        <div x-show="patrullajeItems(grupo).length === 0" class="text-center py-4 text-slate-400 text-xs font-bold italic uppercase tracking-widest opacity-50">
                                            No hay <span x-text="grupo.toLowerCase()"></span> asignados
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Sierra Bravo (Ancho Completo) -->
                        <div class="bg-slate-50/50 border border-slate-100 rounded-2xl overflow-hidden mt-6">
                            <div class="px-4 py-3 bg-white border-b border-slate-100 flex justify-between items-center">
                                <div class="text-[10px] font-black text-blue-600 uppercase tracking-widest">SIERRA BRAVO</div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Todo</span>
                                    <input type="checkbox" 
                                           :checked="patrullajeItems('SIERRA_BRAVO').length > 0 && patrullajeItems('SIERRA_BRAVO').every(p => p.incluido)"
                                           @change="const val = $event.target.checked; patrullajeItems('SIERRA_BRAVO').forEach(p => p.incluido = val)"
                                           class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                                </div>
                            </div>
                            <div class="p-4 space-y-3">
                                <template x-for="p in patrullajeItems('SIERRA_BRAVO')" :key="p.id_campo">
                                    <div class="flex items-center space-x-4 bg-white p-3 rounded-xl border border-slate-50 shadow-sm transition-all hover:border-blue-100 group">
                                        <input type="checkbox" x-model="p.incluido" class="w-5 h-5 text-blue-600 border-slate-200 rounded-lg focus:ring-blue-500 cursor-pointer">
                                        <div class="w-32 flex-shrink-0">
                                            <span class="text-xs font-black text-slate-700 uppercase" x-text="p.display"></span>
                                        </div>
                                        <div class="flex-1">
                                            <input type="text"
                                                   x-model="p.observacion"
                                                   class="w-full bg-slate-50 border border-transparent rounded-lg px-4 py-2 text-xs font-bold text-slate-600 focus:bg-white focus:border-blue-400 focus:ring-0 outline-none uppercase placeholder-slate-300 transition-all"
                                                   placeholder="SIN NOVEDAD">
                                        </div>
                                    </div>
                                </template>
                                <div x-show="patrullajeItems('SIERRA_BRAVO').length === 0" class="text-center py-4 text-slate-400 text-xs font-bold italic uppercase tracking-widest opacity-50">
                                    No hay sierra bravo asignados
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="patrullajeAutomatico.length === 0" class="text-center py-12 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm font-black uppercase tracking-widest">No hay unidades configuradas en el reporte principal</p>
                        <p class="text-[10px] text-slate-300 font-bold mt-2 uppercase">Asigna personal en 'Distribución de Campo' para verlas aquí</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-8 bg-white border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <button type="button" @click="patrullajeAutomatico.forEach(p => p.observacion = '')" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-red-500 flex items-center transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Limpiar todas las novedades
            </button>
            
            <div class="flex items-center space-x-4">
                <button type="button" @click="close()" class="px-8 py-3 rounded-2xl border-2 border-orange-400 text-orange-500 font-black text-xs uppercase tracking-widest hover:bg-orange-50 transition-all flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Cancelar</span>
                </button>
                <button type="button" 
                        @click="generateAndCopyReport()"
                        class="px-10 py-3 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-100 transition-all transform active:scale-95 flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    <span x-text="editingReportId ? 'Actualizar Reporte' : 'Guardar Reporte'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
