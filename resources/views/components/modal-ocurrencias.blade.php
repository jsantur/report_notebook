<div 
    x-data="{ 
        show: false,
        form: {
            hora: '',
            fecha: '',
            turno: '',
            detalle: ''
        },
    init() {
                // Vigilar el store global de cámaras para actualizaciones automáticas
                this.$watch('$store.camaras.total', () => this.updateTextWithStoreCounts());
                this.$watch('$store.camaras.postes', () => this.updateTextWithStoreCounts());

                // Scroll to top when modal opens
                this.$watch('show', (value) => {
                    if (value) {
                        this.$nextTick(() => {
                            const modalContainer = this.$el;
                            if (modalContainer) {
                                modalContainer.scrollTop = 0;
                            }
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    }
                });
            },

    updateTextWithStoreCounts() {
        if (!this.form.detalle || !this.form.detalle.includes('CÁMARAS OPERATIVAS')) return;

        const total = Alpine.store('camaras').total;
        const postes = Alpine.store('camaras').postes;

        const totalStr = `(${String(total).padStart(2, '0')}) CÁMARAS OPERATIVAS ✅`;
        const postesStr = `(${String(postes).padStart(2, '0')}) POSTES DE EMERGENCIA ACTIVOS 🚨`;

        let lines = this.form.detalle.split('\n');
        let changed = false;

        const newLines = lines.map(line => {
            if (line.includes('CÁMARAS OPERATIVAS')) {
                changed = true;
                return totalStr;
            }
            if (line.includes('POSTES DE EMERGENCIA ACTIVOS')) {
                changed = true;
                return postesStr;
            }
            return line;
        });

        if (changed) {
            this.form.detalle = newLines.join('\n');
        }
    },

    openModal() { 
        this.show = true; 
        // Establecer hora actual del cliente
        const now = new Date();
        this.form.hora = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
        
        // Actualizar contadores al abrir
        this.updateTextWithStoreCounts();
    },
    openModalForEdit(data) {
        console.log('Abriendo modal para editar:', data);
        this.form.hora = data.hora;
        this.form.detalle = data.detalle;
        this.show = true;
        // Actualizar contadores al abrir
        this.updateTextWithStoreCounts();
    },
    closeModal() { this.show = false; },
    deleteForm() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Se descartará esta ocurrencia',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                this.form = {
                    hora: '',
                    fecha: '',
                    turno: '',
                    detalle: ''
                };
                window.dispatchEvent(new CustomEvent('ocurrencia-deleted'));
                this.closeModal();
            }
        });
    },
    saveForm() {
        // Validaciones con la misma vistosidad que PNP
        if (!this.form.hora || this.form.hora.length < 5) {
            Swal.fire({
                title: 'Atención',
                text: 'Por favor, ingrese una hora válida (HH:MM)',
                icon: 'warning',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        if (!this.form.detalle || this.form.detalle.trim().length < 5) {
            Swal.fire({
                title: 'Atención',
                text: 'El detalle de la ocurrencia es demasiado corto o está vacío',
                icon: 'warning',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }
        
        // Guardar en el formulario principal del reporte
        const eventData = {
            hora: this.form.hora,
            detalle: this.form.detalle,
            isSaved: true
        };
        
        console.log('Enviando evento ocurrencia-saved:', eventData);
        window.dispatchEvent(new CustomEvent('ocurrencia-saved', {
            detail: eventData
        }));
        
        console.log('Evento enviado, limpiando y cerrando modal');
        
        // Limpiar y cerrar
        this.form = {
            hora: '',
            fecha: '',
            turno: '',
            detalle: ''
        };
        this.closeModal();
    },
    // Función para forzar mayúsculas
    toUpperCase(event) {
        this.form.detalle = event.target.value.toUpperCase();
    },
    // Función para formatear hora automáticamente
    formatHora(value) {
        let formatted = value.replace(/[^\d]/g, '');
        
        // Agregar : después de 2 dígitos
        if (formatted.length >= 3) {
            formatted = formatted.slice(0, 2) + ':' + formatted.slice(2, 4);
        }
        
        // Validar hora
        if (formatted.length === 5) {
            const [hours, minutes] = formatted.split(':');
            const h = parseInt(hours);
            const m = parseInt(minutes);
            
            if (h > 23) formatted = '23:' + minutes;
            if (m > 59) formatted = hours + ':59';
        }
        
        return formatted;
    },

    insertTemplate(event) {
        const total = Alpine.store('camaras').total;
        const postes = Alpine.store('camaras').postes;

        const template = `🔄 RELEVO DE PERSONAL
SE EFECTUÓ EL RELEVO CON EL PERSONAL SALIENTE, RECIBIENDO LAS NOVEDADES Y CONSIGNAS RUTINARIAS CORRESPONDIENTES.

🎥 ESTADO OPERATIVO

(${String(total).padStart(2, '0')}) CÁMARAS OPERATIVAS ✅
(${String(postes).padStart(2, '0')}) POSTES DE EMERGENCIA ACTIVOS 🚨

📦 MATERIAL LOGÍSTICO VERIFICADO

(24) MONITORES 🖥️
(01) RADIO TETRA N.° 73027 “ANTIGUA” 📻
(02) BATERÍAS 🔋
(01) CARGADOR 🔌
(04) RADIOS MOTOROLA “NUEVOS” 📡
(01) EXTINTOR 🧯
(02) VENTILADORES GRANDES 🌬️
(01) STAND 🗂️`;

        // Si se llama automáticamente o no hay evento/target
        if (!event || !event.target) {
            if (this.form.detalle.trim() === '' || !this.form.detalle.includes('CÁMARAS OPERATIVAS')) {
                this.form.detalle = template;
            } else {
                this.updateTextWithStoreCounts();
            }
            return;
        }

        if (this.form.detalle.trim() === '') {
            this.form.detalle = template;
        } else {
            const start = event.target.selectionStart;
            const end = event.target.selectionEnd;
            const text = this.form.detalle;
            this.form.detalle = text.substring(0, start) + template + text.substring(end);
            this.$nextTick(() => {
                event.target.selectionStart = event.target.selectionEnd = start + template.length;
                event.target.focus();
            });
        }
    }
}"
@auto-distribuir-finalizado.window="insertTemplate()"
@keydown.escape.window="closeModal()"
@abrir-modal-ocurrencia.window="openModal()"
@editar-ocurrencia.window="openModalForEdit($event.detail)"
x-show="show"
x-cloak
style="display: none;"
class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 sm:pt-20 overflow-y-auto overflow-x-hidden"
>
    <!-- Fondo oscuro con Blur (Backdrop) -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
    ></div>

    <!-- Contenedor principal del Modal -->
    <div 
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="relative w-full max-w-2xl transform overflow-hidden rounded-2xl bg-white text-left align-middle shadow-2xl transition-all"
        @click.stop
    >
        <!-- Header Azul Degradado (Diseño Estandarizado) -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-6 py-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <!-- Icono App con efecto Pop-out -->
                    <div class="relative flex-shrink-0 w-20 h-16 flex items-center justify-center">
                        <img src="{{ asset('img/logo_ocurrencia.png') }}" alt="Logo" class="absolute w-[150%] h-[250%] object-contain -top-12 -left-2 drop-shadow-lg">
                    </div>
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white">
                            Ocurrencias de Relevo
                        </h3>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest opacity-80">
                            AUTO-MAYÚSCULAS • ENTER PARA AVANZAR ENTRE CAMPOS
                        </p>
                        <div class="flex flex-col space-y-0.5 mt-1">
                            <p class="text-[9px] text-yellow-300 font-bold flex items-center uppercase tracking-wider">
                                <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                Presiona ESC para cerrar la ventana
                            </p>
                            <p class="text-[9px] text-yellow-100 font-bold flex items-center uppercase tracking-wider opacity-90">
                                <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Presiona TAB para insertar plantilla de relevo
                            </p>
                        </div>
                    </div>
                </div>
                <button 
                    @click="closeModal()"
                    class="text-white/80 hover:text-white focus:outline-none transition-colors"
                >
                    <span class="sr-only">Cerrar modal</span>
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Cuerpo del Modal (Formulario) -->
        <div class="px-6 py-6">
            <div class="space-y-6">
                <!-- Sección: Información temporal -->
                <div>
                    <label for="hora" class="block text-sm font-semibold text-gray-700">Hora</label>
                    <input 
                        type="time" 
                        id="hora" 
                        x-model="form.hora"
                        class="mt-1.5 block w-32 rounded-lg border-gray-300 px-4 py-2.5 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all cursor-pointer"
                        required
                    >
                </div>

                <!-- Sección: Detalle de ocurrencia -->
                <div>
                    <label for="detalle" class="sr-only">Detalle de incidencia</label>
                    <textarea 
                        id="detalle" 
                        rows="5" 
                        x-model="form.detalle"
                        @input="toUpperCase"
                        @keydown.tab.prevent="insertTemplate($event)"
                        class="block w-full rounded-lg border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all resize-none"
                        placeholder="ESCRIBE LOS DETALLES AQUÍ... (Presiona TAB para autocompletar la plantilla)"
                        required
                    ></textarea>
                </div>
            </div>
        </div>

        <!-- Footer / Acciones -->
        <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 gap-3 sm:gap-0 border-t border-gray-200">
            <!-- Botón Eliminar -->
            <button 
                type="button" 
                @click="deleteForm()"
                class="inline-flex w-full justify-center items-center rounded-lg border border-red-300 bg-white px-5 py-2.5 text-sm font-semibold text-red-700 shadow-sm hover:bg-red-50 hover:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto transition-all"
            >
                <svg class="-ml-1 mr-2 h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                ELIMINAR
            </button>
            
            <!-- Botón Cancelar -->
            <button 
                type="button" 
                @click="closeModal()"
                class="inline-flex w-full justify-center items-center rounded-lg border-2 border-orange-100 bg-white px-5 py-2.5 text-sm font-bold text-orange-600 shadow-sm hover:bg-orange-50 hover:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 sm:w-auto transition-all"
            >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                CANCELAR
            </button>
            
            <!-- Botón Guardar -->
            <button 
                type="button" 
                @click="saveForm()"
                class="inline-flex w-full justify-center items-center rounded-lg border border-transparent bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto transition-all"
            >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" />
                </svg>
                GUARDAR
            </button>
        </div>
    </div>
</div>
