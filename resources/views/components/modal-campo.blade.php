<div 
    x-data="{ 
        show: false,
        showPatrullajeIntegrado: false,
        tempCampo: {
            id: null,
            tipo_patrullaje: '',
            descripcion: '',
            ubicacion: '',
            celular: '',
            unidad: '',
            matricula: '',
            subtipo_vehiculo: '',
            chofer: '',
            operador: '',
            lince: '',
            sereno: '',
            cantidad: 1,
            incluido: true,
            patrullaje_integrado: []
        },
        tiposPatrullaje: ['Vehicular', 'Motorizado', 'A pie', 'Cecom', 'Prevención'],
        descripcionesPatrullaje: {
            'Vehicular': ['HALCÓN', 'Patrullaje Integrado', 'Rescate'],
            'Motorizado': ['Cazador'],
            'A pie': ['Sierra Bravo', 'Zonas Comerciales'],
            'Cecom': ['Apoyo'],
            'Prevención': ['Apoyo']
        },
        tempIntegrado: {
            id: null,
            nombre: '',
            apellidos: '',
            dni: '',
            imei: '',
            grado: '',
            comisaria: ''
        },
        sectores: ['Norte', 'Centro', 'Sur', 'Enace'],
        unidades: {
            @foreach($vehiculos->where('tipo_patrullaje', 'Vehicular') as $v)
                    '{{ str_replace("Unidad ", "", $v->nro_unidad) }}': { placa: '{{ $v->placa }}', tipo: '{{ $v->tipo }}', emoji: '{{ $v->tipo == "CAMIONETA" ? "🚙" : "🏍️" }}' },
            @endforeach
        },
        motorizadas: {
            @foreach($vehiculos->where('tipo_patrullaje', 'Motorizado') as $v)
                    '{{ str_replace("Unidad ", "", $v->nro_unidad) }}': { placa: '{{ $v->placa }}' },
            @endforeach
        },
        modalSearch: {
            query: '',
            results: [],
            activeField: '',
            activeIndex: 0,
            validSelections: { chofer: '', operador: '', lince: '', sereno: '' }
        },
        skipTipoPatrullajeWatcher: false,

        init() {
            this.$watch('tempCampo.tipo_patrullaje', (value) => {
                if (this.skipTipoPatrullajeWatcher) return;
                if (!value) return;

                // Al cambiar modalidad, limpiamos datos previos para nuevo registro
                // pero preservamos el ID si estamos en modo edición
                const currentId = this.tempCampo.id;
                
                this.tempCampo = {
                    id: currentId,
                    tipo_patrullaje: value,
                    descripcion: '',
                    ubicacion: '',
                    celular: '000000000',
                    unidad: '',
                    matricula: 'PENDIENTE',
                    subtipo_vehiculo: '',
                    chofer: '',
                    operador: '',
                    lince: '',
                    sereno: '',
                    cantidad: ['A pie', 'Cecom', 'Prevención'].includes(value) ? 1 : '',
                    incluido: true,
                    patrullaje_integrado: []
                };

                // Auto-asignación de descripción
                if (value === 'Vehicular') this.tempCampo.descripcion = 'HALCÓN';
                else if (value === 'Motorizado') this.tempCampo.descripcion = 'CAZADOR';
                else if (value === 'A pie') this.tempCampo.descripcion = 'SIERRA BRAVO';
                else if (value === 'Cecom' || value === 'Prevención') this.tempCampo.descripcion = 'APOYO';
            });

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

        open(item = null) {
            if (item) {
                this.skipTipoPatrullajeWatcher = true;
                this.tempCampo = JSON.parse(JSON.stringify(item));
                this.modalSearch.validSelections = {
                    chofer: this.tempCampo.chofer || '',
                    operador: this.tempCampo.operador || '',
                    lince: this.tempCampo.lince || '',
                    sereno: this.tempCampo.sereno || ''
                };
                this.$nextTick(() => { this.skipTipoPatrullajeWatcher = false; });
            } else {
                this.reset();
            }
            this.show = true;
        },

        reset() {
            this.tempCampo = {
                id: null,
                tipo_patrullaje: '',
                descripcion: '',
                ubicacion: '',
                celular: '',
                unidad: '',
                matricula: '',
                subtipo_vehiculo: '',
                chofer: '',
                operador: '',
                lince: '',
                sereno: '',
                cantidad: 1,
                incluido: true,
                patrullaje_integrado: []
            };
            this.modalSearch.validSelections = { chofer: '', operador: '', lince: '', sereno: '' };
            this.showPatrullajeIntegrado = false;
        },

        async searchPersonnel(field) {
            this.modalSearch.activeField = field;
            this.modalSearch.activeIndex = 0; // Reset active index when searching
            this.modalSearch.query = this.tempCampo[field] || '';
            const response = await fetch(`{{ route('api.serenazgo.search') }}?search=${encodeURIComponent(this.modalSearch.query)}&activo=1&json=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            this.modalSearch.results = await response.json();
        },
        handleKeydown(event, field) {
            if (this.modalSearch.activeField !== field || this.modalSearch.results.length === 0) return;
            const key = event.key;
            const maxIndex = this.modalSearch.results.length - 1;
            
            if (key === 'ArrowDown') {
                event.preventDefault();
                this.modalSearch.activeIndex = Math.min(this.modalSearch.activeIndex + 1, maxIndex);
                this.scrollToActive();
            } else if (key === 'ArrowUp') {
                event.preventDefault();
                this.modalSearch.activeIndex = Math.max(this.modalSearch.activeIndex - 1, 0);
                this.scrollToActive();
            } else if (key === 'Enter') {
                event.preventDefault();
                this.selectPerson(this.modalSearch.results[this.modalSearch.activeIndex]);
            } else if (key === 'Escape') {
                this.modalSearch.results = [];
                this.modalSearch.activeField = '';
            }
        },
        scrollToActive() {
            this.$nextTick(() => {
                const dropdown = document.getElementById(`dropdown-${this.modalSearch.activeField}`);
                if (dropdown) {
                    const activeElement = dropdown.children[this.modalSearch.activeIndex];
                    if (activeElement) {
                        activeElement.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                }
            });
        },

        selectPerson(person) {
            const fullName = `${person.apellido_paterno} ${person.apellido_materno || ''}, ${person.nombres}`.replace(/\s+/g, ' ').trim().toUpperCase();
            
            // Validaciones
            const mainEl = document.getElementById('reporte-main');
            const mainData = Alpine.$data(mainEl);
            
            const isAlreadyInModal = [this.tempCampo.chofer, this.tempCampo.operador, this.tempCampo.lince, this.tempCampo.sereno].some(val => val === fullName);
            if (isAlreadyInModal) {
                Swal.fire('Atención', `${fullName} ya ha sido seleccionado en otro cargo.`, 'warning');
                return;
            }

            const isAlreadyInOtherGroup = mainData.selectedCampo.some(item => {
                if (this.tempCampo.id && item.id === this.tempCampo.id) return false;
                return item.chofer === fullName || item.operador === fullName || item.lince === fullName || item.sereno === fullName;
            });

            if (isAlreadyInOtherGroup) {
                Swal.fire('Atención', `${fullName} ya está asignado en otro registro.`, 'warning');
                return;
            }

            this.tempCampo[this.modalSearch.activeField] = fullName;
            this.modalSearch.validSelections[this.modalSearch.activeField] = fullName;
            
            if (['A pie', 'Prevención', 'Cecom'].includes(this.tempCampo.tipo_patrullaje) && this.modalSearch.activeField === 'sereno' && person.celular) {
                this.tempCampo.celular = person.celular;
            }
            if (this.tempCampo.tipo_patrullaje === 'Motorizado' && this.modalSearch.activeField === 'chofer' && person.celular) {
                this.tempCampo.celular = person.celular;
            }

            this.modalSearch.results = [];
            this.modalSearch.query = '';
            this.modalSearch.activeField = '';
        },

        save() {
            if (!this.tempCampo.tipo_patrullaje) {
                Swal.fire('Atención', 'Seleccione una Modalidad de patrullaje', 'warning');
                return;
            }

            const modalidad = this.tempCampo.tipo_patrullaje;

            const validateSelection = (field, label) => {
                if (this.tempCampo[field] && this.tempCampo[field] !== this.modalSearch.validSelections[field]) {
                    Swal.fire('Atención', `Debe seleccionar un ${label} válido de la lista desplegable. No se permite texto libre.`, 'warning');
                    return false;
                }
                return true;
            };

            // Validación por Modalidad (Similar a sub-modal PNP)
            if (modalidad === 'Vehicular') {
                if (!this.tempCampo.ubicacion) {
                    Swal.fire('Atención', 'Por favor, seleccione la Zona / Sector', 'warning');
                    return;
                }
                if (!this.tempCampo.unidad) {
                    Swal.fire('Atención', 'Por favor, seleccione la Unidad vehicular', 'warning');
                    return;
                }
                if (!this.tempCampo.chofer) {
                    Swal.fire('Atención', 'Por favor, seleccione el Chofer asignado', 'warning');
                    return;
                }
                if (!validateSelection('chofer', 'Chofer')) return;
                
                if (this.tempCampo.ubicacion !== 'Comisión' && !this.tempCampo.operador) {
                    Swal.fire('Atención', 'Por favor, seleccione el Operador asignado', 'warning');
                    return;
                }
                if (this.tempCampo.operador && !validateSelection('operador', 'Operador')) return;
                if (this.tempCampo.lince && !validateSelection('lince', 'Lince')) return;
                
            } else if (modalidad === 'Motorizado') {
                if (!this.tempCampo.ubicacion) {
                    Swal.fire('Atención', 'Por favor, seleccione la Zona / Sector', 'warning');
                    return;
                }
                if (!this.tempCampo.chofer) {
                    Swal.fire('Atención', 'Por favor, seleccione el Chofer asignado', 'warning');
                    return;
                }
                if (!validateSelection('chofer', 'Chofer')) return;
                
                if (!this.tempCampo.unidad) {
                    Swal.fire('Atención', 'Por favor, seleccione la Unidad motorizada', 'warning');
                    return;
                }
            } else if (['A pie', 'Cecom', 'Prevención'].includes(modalidad)) {
                if (!this.tempCampo.sereno) {
                    Swal.fire('Atención', 'Por favor, seleccione el Sereno asignado', 'warning');
                    return;
                }
                if (!validateSelection('sereno', 'Sereno')) return;
                
                if (!this.tempCampo.ubicacion) {
                    Swal.fire('Atención', 'Por favor, seleccione la Zona / Sector', 'warning');
                    return;
                }
                if (!this.tempCampo.cantidad || this.tempCampo.cantidad < 1) {
                    Swal.fire('Atención', 'Por favor, ingrese el N° de Puesto', 'warning');
                    return;
                }
            }

            const isEdit = !!this.tempCampo.id;
            window.dispatchEvent(new CustomEvent('campo-record-saved', { detail: { record: JSON.parse(JSON.stringify(this.tempCampo)) } }));
            
            if (isEdit) {
                this.show = false;
            } else {
                this.reset();
                Swal.fire({
                    title: '¡Registrado!',
                    text: 'Personal añadido. Puedes registrar otro o cerrar la ventana.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        },

        // Patrullaje Integrado
        resetIntegrado() {
            this.tempIntegrado = { id: null, nombre: '', apellidos: '', dni: '', imei: '', grado: '', comisaria: '' };
        },
        addIntegrado() {
            // Validaciones específicas
            if (!this.tempIntegrado.grado) {
                Swal.fire('Atención', 'Por favor, seleccione el Grado PNP', 'warning');
                return;
            }
            if (!this.tempIntegrado.comisaria) {
                Swal.fire('Atención', 'Por favor, seleccione la Comisaría', 'warning');
                return;
            }
            if (!this.tempIntegrado.nombre || this.tempIntegrado.nombre.trim().length < 2) {
                Swal.fire('Atención', 'Por favor, ingrese los Nombres PNP', 'warning');
                return;
            }
            if (!this.tempIntegrado.apellidos || this.tempIntegrado.apellidos.trim().length < 2) {
                Swal.fire('Atención', 'Por favor, ingrese los Apellidos PNP', 'warning');
                return;
            }
            if (!this.tempIntegrado.dni || this.tempIntegrado.dni.length !== 8) {
                Swal.fire('Atención', 'El DNI debe tener exactamente 8 dígitos', 'warning');
                return;
            }

            if (this.tempIntegrado.id) {
                const idx = this.tempCampo.patrullaje_integrado.findIndex(i => i.id === this.tempIntegrado.id);
                if (idx !== -1) this.tempCampo.patrullaje_integrado[idx] = { ...this.tempIntegrado };
            } else {
                this.tempCampo.patrullaje_integrado.push({ ...this.tempIntegrado, id: Date.now() });
            }
            
            this.showPatrullajeIntegrado = false; // Cierra el sub-modal
            this.resetIntegrado();
        },
        editIntegrado(item) { 
            this.tempIntegrado = { ...item }; 
            this.showPatrullajeIntegrado = true;
            this.$nextTick(() => { if(this.$refs.nombrePNP) this.$refs.nombrePNP.focus(); });
        },
        removeIntegrado(id) { 
            this.tempCampo.patrullaje_integrado = this.tempCampo.patrullaje_integrado.filter(i => i.id !== id); 
        },
        consultarDni() {
            const dni = this.tempIntegrado.dni?.trim();
            if (!dni || dni.length !== 8) {
                Swal.fire('Atención', 'Ingrese un DNI válido de 8 dígitos', 'warning');
                return;
            }

            Swal.fire({
                title: 'Consultando...',
                text: 'Buscando datos del DNI',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('api.consultar.dni') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ dni: dni })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.tempIntegrado.nombre = data.data.nombres;
                    this.tempIntegrado.apellidos = `${data.data.apellido_paterno} ${data.data.apellido_materno}`;
                    Swal.close();
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Datos autocompletados correctamente.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire('Atención', data.message || 'No se encontraron resultados para este DNI.', 'info');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al consultar el DNI.', 'error');
            });
        }
    }"
    @abrir-modal-campo.window="open($event.detail)"
    @keydown.escape.window="if(showPatrullajeIntegrado) { showPatrullajeIntegrado = false; resetIntegrado(); } else { show = false; }"
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
        class="relative w-full max-w-6xl transform overflow-hidden rounded-[32px] bg-white shadow-2xl transition-all"
        @click.stop
    >
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-8 py-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-white/20 p-2.5 rounded-2xl text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-white" x-text="tempCampo.id ? 'Editar Personal de Campo' : 'Agregar Personal de Campo'"></h3>
                        <p class="text-[10px] font-bold text-blue-100 uppercase tracking-widest opacity-80">Configuración de Unidades y Zonas de Patrullaje</p>
                        <p class="text-[9px] text-yellow-300 font-bold mt-1 flex items-center uppercase tracking-wider">
                            <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Presiona ESC para cerrar la ventana
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

        <div class="p-8 overflow-y-auto custom-scrollbar bg-slate-50 max-h-[80vh]">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Columna Izquierda: Configuración Base (4 cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Tipo de Servicio
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Modalidad:</label>
                                <select x-model="tempCampo.tipo_patrullaje" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 transition-all">
                                    <option value="" disabled>Seleccione tipo</option>
                                    <template x-for="t in tiposPatrullaje" :key="t">
                                        <option :value="t" x-text="t"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Descripción:</label>
                                <div class="w-full bg-slate-100 border border-slate-200 rounded-xl py-3 px-4 flex items-center h-[46px]">
                                    <span class="text-sm font-bold text-slate-700 uppercase" 
                                          x-text="tempCampo.descripcion || 'PENDIENTE'"></span>
                                </div>
                            </div>

                            <template x-if="['A pie', 'Prevención', 'Cecom'].includes(tempCampo.tipo_patrullaje)">
                                <div class="pt-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">N° de Puesto:</label>
                                    <input type="number" x-model="tempCampo.cantidad" min="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-center focus:ring-2 focus:ring-blue-500 outline-none text-lg font-black text-blue-600">
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Ubicación y Contacto
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Zona / Sector:</label>
                                <select x-model="tempCampo.ubicacion" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 transition-all">
                                    <option value="" disabled>Seleccione ubicación</option>
                                    <template x-for="z in sectores" :key="z">
                                        <option :value="z" x-text="z"></option>
                                    </template>
                                    <template x-if="['Vehicular', 'Motorizado'].includes(tempCampo.tipo_patrullaje)">
                                        <option value="Comisión">Comisión</option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Celular de Contacto:</label>
                                <div class="relative">
                                    <input type="text" x-model="tempCampo.celular" @input="tempCampo.celular = tempCampo.celular.replace(/[^0-9]/g, '')" maxlength="9" placeholder="999888777" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-10 pr-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 transition-all">
                                    <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Personal y Vehículos (8 cols) -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Sección de Vehículos (Si aplica) -->
                    <template x-if="['Vehicular', 'Motorizado'].includes(tempCampo.tipo_patrullaje)">
                        <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100 animate-fade-in">
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                Datos de la Unidad
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Unidad:</label>
                                    <select x-model="tempCampo.unidad" 
                                            @change="
                                                if(tempCampo.tipo_patrullaje === 'Vehicular') {
                                                    const u = unidades[tempCampo.unidad];
                                                    tempCampo.matricula = u.placa;
                                                    tempCampo.subtipo_vehiculo = u.tipo;
                                                } else {
                                                    tempCampo.matricula = motorizadas[tempCampo.unidad].placa;
                                                }
                                            "
                                            class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 transition-all">
                                        <option value="" disabled>Seleccione unidad</option>
                                        <template x-for="(data, unit) in (tempCampo.tipo_patrullaje === 'Vehicular' ? unidades : motorizadas)" :key="unit">
                                            <option :value="unit" 
                                                    :disabled="(() => {
                                                        const mainEl = document.getElementById('reporte-main');
                                                        if (!mainEl) return false;
                                                        const mainData = Alpine.$data(mainEl);
                                                        return mainData.selectedCampo.some(item => 
                                                            item.tipo_patrullaje === tempCampo.tipo_patrullaje && 
                                                            item.unidad === unit && 
                                                            item.id !== tempCampo.id
                                                        );
                                                    })()"
                                                    x-text="unit"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Placa / Matrícula:</label>
                                    <div class="bg-blue-50 border-2 border-blue-100 rounded-xl py-2.5 px-4 flex justify-between items-center h-[46px]">
                                        <span class="text-sm font-black text-blue-700 tracking-wider" x-text="tempCampo.matricula || 'PENDIENTE'"></span>
                                        <template x-if="tempCampo.subtipo_vehiculo">
                                            <span class="text-[9px] bg-blue-600 text-white px-2 py-0.5 rounded-full font-bold uppercase" x-text="tempCampo.subtipo_vehiculo"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Sección de Personal -->
                    <div class="bg-white p-6 rounded-[24px] shadow-sm border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Asignación de Personal
                        </h4>
                        
                        <!-- Grid uniforme de 3 columnas para Vehicular -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4"
                             x-show="['Vehicular', 'Motorizado'].includes(tempCampo.tipo_patrullaje)"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            
                            <!-- Chofer (visible para Vehicular y Motorizado) -->
                            <div :class="tempCampo.tipo_patrullaje === 'Motorizado' ? 'md:col-span-3' : ''">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Seleccionar Chofer:</label>
                                <div class="relative">
                                    <input type="text" x-model="tempCampo.chofer" 
                                           @click="modalSearch.query = ''; searchPersonnel('chofer')" 
                                           @input="tempCampo.chofer = tempCampo.chofer.replace(/[0-9]/g, '')"
                                           @input.debounce.300ms="searchPersonnel('chofer')" 
                                           @keydown="handleKeydown($event, 'chofer')"
                                           @blur="setTimeout(() => { if(modalSearch.activeField === 'chofer') { modalSearch.results = []; modalSearch.activeField = ''; } }, 200)"
                                           placeholder="Buscar chofer..." 
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 uppercase transition-all">
                                    <div id="dropdown-chofer"
                                         x-show="modalSearch.activeField === 'chofer' && modalSearch.results.length > 0" 
                                         class="absolute z-10 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden max-h-48 overflow-y-auto">
                                        <template x-for="(p, index) in modalSearch.results" :key="p.id">
                                            <div @click="selectPerson(p)" 
                                                 :class="index === modalSearch.activeIndex && modalSearch.activeField === 'chofer' ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-50 text-slate-600'"
                                                 class="p-4 cursor-pointer text-xs font-bold border-b border-slate-50 last:border-0 uppercase transition-colors" 
                                                 x-text="`${p.apellido_paterno} ${p.apellido_materno || ''}, ${p.nombres}`.replace(/\s+/g, ' ').trim()"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Operador (solo Vehicular) -->
                            <div x-show="tempCampo.tipo_patrullaje === 'Vehicular'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Seleccionar Operador:</label>
                                <div class="relative">
                                    <input type="text" x-model="tempCampo.operador" 
                                           @click="modalSearch.query = ''; searchPersonnel('operador')" 
                                           @input="tempCampo.operador = tempCampo.operador.replace(/[0-9]/g, '')"
                                           @input.debounce.300ms="searchPersonnel('operador')" 
                                           @keydown="handleKeydown($event, 'operador')"
                                           @blur="setTimeout(() => { if(modalSearch.activeField === 'operador') { modalSearch.results = []; modalSearch.activeField = ''; } }, 200)"
                                           placeholder="Buscar operador..." 
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 uppercase transition-all">
                                    <div id="dropdown-operador"
                                         x-show="modalSearch.activeField === 'operador' && modalSearch.results.length > 0" 
                                         class="absolute z-10 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden max-h-48 overflow-y-auto">
                                        <template x-for="(p, index) in modalSearch.results" :key="p.id">
                                            <div @click="selectPerson(p)" 
                                                 :class="index === modalSearch.activeIndex && modalSearch.activeField === 'operador' ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-50 text-slate-600'"
                                                 class="p-4 cursor-pointer text-xs font-bold border-b border-slate-50 last:border-0 uppercase transition-colors" 
                                                 x-text="`${p.apellido_paterno} ${p.apellido_materno || ''}, ${p.nombres}`.replace(/\s+/g, ' ').trim()"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Lince (solo Vehicular) -->
                            <div x-show="tempCampo.tipo_patrullaje === 'Vehicular'"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Seleccionar Lince:</label>
                                <div class="relative">
                                    <input type="text" x-model="tempCampo.lince" 
                                           @click="modalSearch.query = ''; searchPersonnel('lince')" 
                                           @input="tempCampo.lince = tempCampo.lince.replace(/[0-9]/g, '')"
                                           @input.debounce.300ms="searchPersonnel('lince')" 
                                           @keydown="handleKeydown($event, 'lince')"
                                           @blur="setTimeout(() => { if(modalSearch.activeField === 'lince') { modalSearch.results = []; modalSearch.activeField = ''; } }, 200)"
                                           placeholder="Buscar lince..." 
                                           class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 uppercase transition-all">
                                    <div id="dropdown-lince"
                                         x-show="modalSearch.activeField === 'lince' && modalSearch.results.length > 0" 
                                         class="absolute z-10 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden max-h-48 overflow-y-auto">
                                        <template x-for="(p, index) in modalSearch.results" :key="p.id">
                                            <div @click="selectPerson(p)" 
                                                 :class="index === modalSearch.activeIndex && modalSearch.activeField === 'lince' ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-50 text-slate-600'"
                                                 class="p-4 cursor-pointer text-xs font-bold border-b border-slate-50 last:border-0 uppercase transition-colors" 
                                                 x-text="`${p.apellido_paterno} ${p.apellido_materno || ''}, ${p.nombres}`.replace(/\s+/g, ' ').trim()"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sereno (para A pie, Prevención, Cecom) -->
                        <div x-show="['A pie', 'Prevención', 'Cecom'].includes(tempCampo.tipo_patrullaje)"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Seleccionar Sereno:</label>
                            <div class="relative">
                                <input type="text" x-model="tempCampo.sereno" 
                                       @click="modalSearch.query = ''; searchPersonnel('sereno')"
                                       @input="tempCampo.sereno = tempCampo.sereno.replace(/[0-9]/g, '')"
                                       @input.debounce.300ms="searchPersonnel('sereno')"
                                       @keydown="handleKeydown($event, 'sereno')"
                                       @blur="setTimeout(() => { if(modalSearch.activeField === 'sereno') { modalSearch.results = []; modalSearch.activeField = ''; } }, 200)"
                                       placeholder="Buscar por nombre..." 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-sm font-bold text-slate-700 uppercase transition-all">
                                <div id="dropdown-sereno"
                                     x-show="modalSearch.activeField === 'sereno' && modalSearch.results.length > 0" 
                                     class="absolute z-10 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden max-h-48 overflow-y-auto">
                                    <template x-for="(p, index) in modalSearch.results" :key="p.id">
                                        <div @click="selectPerson(p)" 
                                             :class="index === modalSearch.activeIndex && modalSearch.activeField === 'sereno' ? 'bg-blue-100 text-blue-700' : 'hover:bg-blue-50 text-slate-600'"
                                             class="p-4 cursor-pointer text-xs font-bold border-b border-slate-50 last:border-0 uppercase transition-colors" 
                                             x-text="`${p.apellido_paterno} ${p.apellido_materno || ''}, ${p.nombres}`.replace(/\s+/g, ' ').trim()"></div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Patrullaje Integrado (Solo Vehicular) -->
                        <template x-if="tempCampo.tipo_patrullaje === 'Vehicular'">
                            <div class="mt-6 pt-6 border-t border-slate-100">
                                <button type="button" @click="showPatrullajeIntegrado = true; $nextTick(() => $refs.nombrePNP && $refs.nombrePNP.focus())" class="w-full py-4 rounded-2xl bg-blue-50 text-blue-600 font-black text-[10px] uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center space-x-3 group">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <span>Gestionar Patrullaje Integrado (PNP)</span>
                                </button>
                                
                                <template x-if="tempCampo.patrullaje_integrado.length > 0">
                                    <div class="mt-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                        <div class="flex justify-between items-center mb-3">
                                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Personal PNP Asignado</span>
                                            <span class="bg-blue-600 text-white text-[9px] px-2 py-0.5 rounded-full font-bold" x-text="`${tempCampo.patrullaje_integrado.length} Efectivos`"></span>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <template x-for="pnp in tempCampo.patrullaje_integrado" :key="pnp.id">
                                                <div class="bg-white p-3 rounded-xl border border-slate-100 flex justify-between items-center shadow-sm">
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] font-black text-slate-700 uppercase" x-text="`${pnp.grado} ${pnp.apellidos}`"></span>
                                                        <span class="text-[9px] text-slate-400 font-bold uppercase" x-text="`DNI: ${pnp.dni}`"></span>
                                                    </div>
                                                    <div class="flex items-center space-x-1">
                                                        <button @click="editIntegrado(pnp)" class="text-blue-400 hover:text-blue-600 p-1.5 hover:bg-blue-50 rounded-lg transition-all">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                        </button>
                                                        <button @click="removeIntegrado(pnp.id)" class="text-red-400 hover:text-red-600 p-1.5 hover:bg-red-50 rounded-lg transition-all">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-8 bg-white border-t border-slate-100 flex justify-between items-center">
            <button type="button" @click="show = false" class="px-8 py-3 rounded-2xl border-2 border-orange-400 text-orange-500 font-black text-xs uppercase tracking-widest hover:bg-orange-50 transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                <span>CANCELAR</span>
            </button>
            <button type="button" @click="save()" class="px-12 py-3 rounded-2xl bg-blue-600 text-white font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-100 transition-all transform active:scale-95 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="tempCampo.id ? 'GUARDAR CAMBIOS' : 'REGISTRAR PERSONAL'"></span>
            </button>
        </div>

        <!-- SUB-MODAL: Patrullaje Integrado (Overlay) -->
        <div x-show="showPatrullajeIntegrado" class="absolute inset-0 z-[60] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4" x-transition>
            <div class="bg-white w-full max-w-2xl rounded-[32px] shadow-2xl overflow-hidden" @click.stop>
                <div class="bg-slate-800 p-6 text-white flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 p-2 rounded-xl text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black uppercase tracking-widest">Datos Patrullaje Integrado (PNP)</h4>
                            <p class="text-[9px] text-blue-300 font-bold uppercase mt-0.5 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                Ingresa el DNI para autocompletar datos
                            </p>
                        </div>
                    </div>
                    <button @click="showPatrullajeIntegrado = false" class="text-white/60 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Grado PNP:</label>
                            <select x-model="tempIntegrado.grado" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-xs font-bold text-slate-700">
                                <option value="">— Seleccione Grado —</option>
                                <option>GENERAL PNP</option>
                                <option>CORONEL PNP</option>
                                <option>COMANDANTE PNP</option>
                                <option>MAYOR PNP</option>
                                <option>CAPITÁN PNP</option>
                                <option>TENIENTE PNP</option>
                                <option>ALFÉREZ PNP</option>
                                <option>SUBOFICIAL SUPERIOR PNP</option>
                                <option>SUBOFICIAL BRIGADIER PNP</option>
                                <option>SUBOFICIAL DE PRIMERA PNP</option>
                                <option>SUBOFICIAL DE SEGUNDA PNP</option>
                                <option>SUBOFICIAL DE TERCERA PNP</option>
                                <option>AUXILIAR PNP</option>
                                <option>CADETE PNP</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Comisaría:</label>
                            <select x-model="tempIntegrado.comisaria" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-xs font-bold text-slate-700">
                                <option value="">— Seleccione Comisaría —</option>
                                <option>CPNP TALARA</option>
                                <option>CPNP TALARA ALTA</option>
                            </select>
                        </div>
                        <div class="col-span-2 grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Nombres PNP:</label>
                                <input type="text" x-model="tempIntegrado.nombre" 
                                       x-ref="nombrePNP"
                                       placeholder="Ej. JUAN CARLOS" 
                                       autocomplete="off"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-xs font-bold text-slate-700 uppercase placeholder:font-normal placeholder:normal-case placeholder:text-slate-300">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">Apellidos PNP:</label>
                                <input type="text" x-model="tempIntegrado.apellidos" 
                                       placeholder="Ej. QUISPE MAMANI" 
                                       autocomplete="off"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-xs font-bold text-slate-700 uppercase placeholder:font-normal placeholder:normal-case placeholder:text-slate-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">DNI:</label>
                            <div class="relative flex items-center">
                                <input type="text" x-model="tempIntegrado.dni" maxlength="8" 
                                       placeholder="12345678" 
                                       autocomplete="off"
                                       @input="tempIntegrado.dni = tempIntegrado.dni.replace(/[^0-9]/g, '')"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pr-10 pl-4 focus:ring-2 focus:ring-blue-500 outline-none text-xs font-black text-slate-700 placeholder:font-normal placeholder:text-slate-300 tracking-widest transition-all">
                                <button type="button" @click="consultarDni()" class="absolute right-1 p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-lg transition-colors group" title="Autocompletar nombres y apellidos usando el DNI">
                                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5 ml-1">IMEI / ID:</label>
                            <input type="text" x-model="tempIntegrado.imei" 
                                   placeholder="Ej. 359876543210000" 
                                   maxlength="15"
                                   autocomplete="off"
                                   @input="tempIntegrado.imei = tempIntegrado.imei.replace(/[^0-9]/g, '')"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 focus:ring-2 focus:ring-blue-500 outline-none text-xs font-bold text-slate-700 placeholder:font-normal placeholder:text-slate-300">
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button @click="addIntegrado()" class="bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest px-8 py-3 rounded-xl shadow-lg hover:bg-blue-700 transition-all">
                            <span x-text="tempIntegrado.id ? 'ACTUALIZAR EFECTIVO' : 'AÑADIR EFECTIVO'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
