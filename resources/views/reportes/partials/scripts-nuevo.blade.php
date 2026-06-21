<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/es.js"></script>
<script>
    function reporteData(config) {
        return {
            showModal: false, 
            incidenciaData: { hora: '', detalles: '' },
            isSaved: false,
            tempHora: '',
            tempDetalles: '',
            saveTimeout: null,
            hasUnsavedChanges: false,
            currentUserRole: '{{ Auth::user()->role }}',
            isLiveSyncing: false,
            isCollaborativeEditing: false,
            isReceivingExternalUpdate: false,
            lastSaved: null,
            activeDrafts: [],
            selectedDraftId: null,
            syncInterval: null,
            previouslySyncedData: null,
            lastProcessedDraftHash: null,
            lastNotifiedChangeHash: null,
            reportesUnidades: [],
            isSubmitting: false,
            isResettingForm: false,
            camposFechaHoraTurnoDesbloqueados: false,
            intervaloActivo: false, // Flag manual para controlar el intervalo
            // Para fecha y hora
            fechaActual: '',
            horaActual: '',
            turnoActual: config.turno,
            horaAutoUpdateInterval: null,
            fechaPicker: null,
            horaPicker: null,
            
            handleUnidadesReportSaved({ report, isEdit }) {
                if (!Array.isArray(this.reportesUnidades)) {
                    this.reportesUnidades = [];
                }
                if (isEdit) {
                    const index = this.reportesUnidades.findIndex(r => r.id === report.id);
                    if (index !== -1) {
                        this.reportesUnidades.splice(index, 1, report);
                    }
                } else {
                    this.reportesUnidades.push(report);
                }
                // Forzar reactividad reasignando la variable
                this.reportesUnidades = [...this.reportesUnidades];
                
                this.persistUnidadesReportes();
                this.saveDraft(); // Sync to backend via standard draft saving
                this.triggerNotification(isEdit ? 'Reporte de unidades actualizado' : 'Reporte de unidades guardado');
            },

            persistUnidadesReportes() {
                localStorage.setItem('reporte_unidades_draft', JSON.stringify(this.reportesUnidades));
            },

            editarReporteUnidades(reporte) {
                this.$dispatch('abrir-modal-unidades', {
                    id: reporte.id,
                    hora: reporte.hora,
                    rawData: JSON.parse(JSON.stringify(reporte.rawData))
                });
            },

            eliminarReporteUnidades(id) {
                if (window.Swal) {
                    Swal.fire({
                        title: '¿Eliminar reporte?',
                        text: 'No se podrá revertir',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.reportesUnidades = this.reportesUnidades.filter(r => r.id !== id);
                            this.persistUnidadesReportes();
                            this.saveDraft();
                            this.triggerNotification('Reporte eliminado');
                        }
                    });
                }
            },

            markUnsaved() {
                this.hasUnsavedChanges = true;
            },
            
            clearUnsaved() {
                this.hasUnsavedChanges = false;
            },
            
            eliminarOcurrencia() {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Se eliminará esta ocurrencia',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.eliminarOcurrenciaSilencioso();
                    }
                });
            },
            
            eliminarOcurrenciaSilencioso() {
                this.incidenciaData.hora = '';
                this.incidenciaData.detalles = '';
                this.isSaved = false;
                this.saveDraft();
            },
            
            triggerNotification(msg, type = 'success') {
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: msg, type: type } }));
            },
            
            debugData() {
                console.log('isSaved:', this.isSaved);
                console.log('incidenciaData:', this.incidenciaData);
            },
            
            showOperadoresModal: false,
            operadoresSearch: '',
            operadoresList: [],
            selectedOperadores: [],
            maquinas: config.maquinas || [],
            
            showCampoListaCompletaModal: false,
            skipTipoPatrullajeWatcher: false,
            selectedCampo: [],
            sectores: ['Norte', 'Centro', 'Sur', 'Enace'],
            unidades: config.unidades || {},
            motorizadas: config.motorizadas || {},
            
            patrullajeAutomatico: [],
            patrullandoReportes: [],
            showPatrullandoModal: false,
            editingReportId: null,
            horaReporte: '',

            showCamarasModal: false,
            editingCamaraReportId: null,
            isCorrectingAI: false,
            camarasList: window.camarasFromDB || [],
            cargandoCamarasCSV: false,
            showCamarasAsignacionModal: false,
            asignacionCamaraQuery: '',
            camaraQuery: '',
            camaraFiltered: [],
            camaraIndex: -1,
            tempCamara: {
                nombre: '',
                hora: '',
                descripcion: '',
                descripcion_corregida: ''
            },
            camarasReportes: [],

            textAreas: {
                distribucion_personal_camaras: '',
                distribucion_personal_campo: '',
                visualizaciones_resaltantes: ''
            },

            supervisor_campo_id: '',
            supervisores_camaras: [config.defaultSupervisorCamarasId || ""],
            turno: config.turno,

            get sortedCampoLista() {
                const order = {
                    'Vehicular': 1,
                    'Motorizado': 2,
                    'A pie': 3,
                    'Cecom': 4,
                    'Prevención': 5
                };
                return [...this.selectedCampo].sort((a, b) => {
                    const orderA = order[a.tipo_patrullaje] || 99;
                    const orderB = order[b.tipo_patrullaje] || 99;
                    return orderA - orderB;
                });
            },

            // Inicializar fecha y hora automáticas
            initFechaHora() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                this.fechaActual = `${year}-${month}-${day}`; // YYYY-MM-DD
                this.horaActual = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
            },
            
            // Cargar cámaras desde el CSV via API
            async cargarCamarasDesdeCSV() {
                if (this.cargandoCamarasCSV) return;
                
                this.cargandoCamarasCSV = true;
                console.log('🔄 Iniciando carga de cámaras desde CSV...');
                
                try {
                    const response = await fetch('{{ route('api.hikcentral.status') }}');
                    const data = await response.json();
                    
                    console.log('📥 Datos recibidos del CSV:', data);
                    
                    // Limpiar completamente la lista existente
                    this.camarasList = [];
                    
                    // Convertir datos del CSV al formato del sistema
                    const nuevasCamaras = data.cameras.map(cam => ({
                        name: cam.nombre,
                        operativa: true,
                        ip: cam.ip,
                        puerto: cam.puerto
                    }));
                    
                    // Actualizar la lista con solo las cámaras ONLINE
                    this.camarasList = [...nuevasCamaras];
                    
                    console.log('✅ Lista de cámaras actualizada. Total:', this.camarasList.length);
                    
                } catch (error) {
                    console.error('❌ Error al cargar cámaras desde CSV:', error);
                    this.camarasList = [];
                } finally {
                    this.cargandoCamarasCSV = false;
                }
            },
            
            // Iniciar intervalo de actualización automática de hora
            iniciarAutoUpdateHora() {
                this.detenerAutoUpdateHora();
                this.intervaloActivo = true;
                console.log("🔄 Iniciando intervalo automático de hora...");
                console.log("→ camposFechaHoraTurnoDesbloqueados (al iniciar):", this.camposFechaHoraTurnoDesbloqueados);
                this.horaAutoUpdateInterval = setInterval(() => {
                    console.log("⏱️ Intervalo ejecutándose, camposFechaHoraTurnoDesbloqueados =", this.camposFechaHoraTurnoDesbloqueados);
                    // ¡¡¡IMPORTANTE!!! Si ESTAMOS DESBLOQUEADOS -> NO actualizamos
                    if (this.camposFechaHoraTurnoDesbloqueados) {
                        console.log("🔓 Desbloqueado - NO actualizo hora");
                        return;
                    }
                    const now = new Date();
                    console.log("🔒 Bloqueado - actualizo hora a", String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0'));
                    this.horaActual = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
                }, 1000);
            },
            
            // Detener intervalo de actualización automática de hora
            detenerAutoUpdateHora() {
                console.log("🛑 Deteniendo intervalo de hora...");
                this.intervaloActivo = false;
                if (this.horaAutoUpdateInterval) {
                    clearInterval(this.horaAutoUpdateInterval);
                    this.horaAutoUpdateInterval = null;
                    console.log("✅ Intervalo deteniido");
                }
            },
            
            // Manejar cambio de estado de desbloqueo
            handleUnlockedChange(unlocked) {
                console.log('🔐 handleUnlockedChange llamado:', unlocked);
                this.camposFechaHoraTurnoDesbloqueados = unlocked;
                console.log('🔐 camposFechaHoraTurnoDesbloqueados actualizado a:', this.camposFechaHoraTurnoDesbloqueados);
                
                if (unlocked) {
                    console.log('🔓 Acción: Desbloquear campos');
                    this.detenerAutoUpdateHora();
                } else {
                    console.log("🔒 Acción: Restableciendo valores a actuales...");
                    this.initFechaHora();
                    this.turnoActual = config.turno;
                    this.iniciarAutoUpdateHora();
                }
            },
            
            // Inicializar Flatpickr (Eliminado por causar conflictos de sobreescritura)
            initPickers() {
                console.log("✅ Flatpickr desactivado para permitir escritura manual");
            },
            
            destroyPickers() {
                // Función vacía
            },
            
            init() {
                // 1. PRIMERO: Escuchar el evento de desbloqueo para no perderlo
                const self = this;
                window.addEventListener('unlock-status-changed', (e) => {
                    self.handleUnlockedChange(e.detail.unlocked);
                });

                // 2. SEGUNDO: Chequear localStorage para ver si ya está desbloqueado
                const savedBloqueo = localStorage.getItem('report_password_bloqueada');
                if (savedBloqueo === 'unlocked') {
                    this.camposFechaHoraTurnoDesbloqueados = true;
                }

                // 3. TERCERO: Inicializar fecha y hora
                this.initFechaHora();

                // 4. CUARTO: Iniciar intervalo SOLO SI NO está desbloqueado
                if (!this.camposFechaHoraTurnoDesbloqueados) {
                    this.iniciarAutoUpdateHora();
                } else {
                    // Si ya está desbloqueado, inicializar los pickers inmediatamente
                    this.$nextTick(() => {
                        this.initPickers();
                    });
                }
                
                const estadoCamaras = localStorage.getItem('estado_camaras_global');
                if (estadoCamaras) {
                    try {
                        const camarasState = JSON.parse(estadoCamaras);
                        this.camarasList = this.camarasList.map(c => {
                            const saved = camarasState.find(s => s.name === c.name);
                            if (saved) return { ...c, operativa: saved.operativa };
                            return c;
                        });
                    } catch(e) { console.error('Error al cargar estado de cámaras:', e); }
                }

                let loaded = false;
                const draft = localStorage.getItem('reporte_draft');
                if (draft) {
                    try {
                        const data = JSON.parse(draft);
                        this.loadDraftData(data);
                        loaded = true;
                        this.markUnsaved();
                    } catch (e) {
                        console.error('Error al cargar el borrador:', e);
                        localStorage.removeItem('reporte_draft');
                    }
                }

                const storedUnidades = localStorage.getItem('reporte_unidades_draft');
                if (storedUnidades) {
                    try {
                        this.reportesUnidades = JSON.parse(storedUnidades);
                    } catch(e) {}
                }

                this.checkActiveDrafts().then(() => {
                    if (!loaded && this.currentUserRole !== 'admin' && this.activeDrafts.length > 0) {
                        const myDraft = this.activeDrafts.find(d => d.user_id === {{ Auth::id() }});
                        if (myDraft) {
                            this.loadDraftData(myDraft.data);
                            this.triggerNotification('Borrador restaurado desde el servidor');
                        }
                    }

                    // Auto-Restauración de Sesión Administrador
                    if (this.currentUserRole === 'admin' && config.adminMonitoringUserId && config.adminMonitoringDraftId) {
                        const activeDraft = this.activeDrafts.find(d => String(d.id) === String(config.adminMonitoringDraftId));
                        if (activeDraft) {
                            if (config.adminMonitoringMode === 'collaborative') {
                                this.startCollaborativeEdit(activeDraft, true);
                            } else {
                                this.startLiveSync(activeDraft, true);
                            }
                        }
                    }
                });

                if (this.currentUserRole === 'admin') {
                    setInterval(() => {
                        if (!this.isLiveSyncing && !this.isCollaborativeEditing) {
                            this.checkActiveDrafts();
                        }
                    }, 10000);
                } else {
                    // Si soy supervisor, consultar el borrador cada 5 segundos buscando si el admin modificó algo
                    setInterval(() => {
                        this.checkActiveDrafts().then(() => {
                            if (this.activeDrafts.length > 0) {
                                const myDraft = this.activeDrafts.find(d => d.user_id === {{ Auth::id() }});
                                if (myDraft && myDraft.data && myDraft.data.last_modified_by === 'admin' && myDraft.data.lastSaved) {
                                    const bdTime = myDraft.data.lastSaved || 0;
                                    const localTime = this.lastSaved || 0;
                                    
                                    if (bdTime > localTime) {
                                        this.loadDraftData(myDraft.data, true);
                                    }
                                }
                            }
                        });
                    }, 5000);
                }

                this.syncDraftToAPI();

                this.$watch('incidenciaData', () => this.saveDraft());
                this.$watch('isSaved', () => this.saveDraft());
                this.$watch('selectedOperadores', () => this.saveDraft());
                this.$watch('textAreas', () => this.saveDraft());
                this.$watch('supervisor_campo_id', () => this.saveDraft());
                this.$watch('supervisores_camaras', () => this.saveDraft());
                this.$watch('showPatrullandoModal', (value) => {
                    if (value) {
                        if (!this.editingReportId) {
                            this.patrullajeAutomatico.forEach(p => p.observacion = '');
                            const now = new Date();
                            this.horaReporte = `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
                        }
                        setTimeout(() => {
                            const firstInput = document.querySelector('#patrullandoList input[type=text]');
                            if (firstInput) firstInput.focus();
                        }, 200);
                    } else {
                        this.editingReportId = null;
                    }
                });
                this.$watch('selectedCampo', (value) => {
                    this.syncPatrullaje(value);
                    this.syncDraftToAPI();
                    this.saveDraft();
                });
                this.$watch('patrullajeAutomatico', () => this.saveDraft());
                this.$watch('patrullandoReportes', () => this.saveDraft());
                this.$watch('camarasReportes', () => this.saveDraft());
                this.$watch('reportesUnidades', () => this.saveDraft());
                this.$watch('turno', () => this.saveDraft());

                // Scroll to top for remaining modals
                this.$watch('showCamarasAsignacionModal', (value) => {
                    if (value) {
                        // Cargar las cámaras desde el CSV automáticamente
                        this.cargarCamarasDesdeCSV();
                        this.$nextTick(() => {
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    }
                });

                this.$watch('showCampoListaCompletaModal', (value) => {
                    if (value) {
                        this.$nextTick(() => {
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    }
                });

                this.$watch('patrullajeAutomatico', (value) => {
                    value.forEach(p => {
                        if (p.grupo === 'SIERRA_BRAVO') {
                            const numero = p.id_campo.replace('sb-', '');
                            const unit = this.selectedCampo.find(c => c.tipo_patrullaje === 'A pie' && String(c.cantidad) === String(numero));
                            if (unit) unit.incluido = p.incluido;
                        } else {
                            const unit = this.selectedCampo.find(c => c.id === p.id_campo);
                            if (unit) unit.incluido = p.incluido;
                        }
                    });
                }, { deep: true });
                
                Alpine.store('camaras').update(this.camarasList);
                this.$watch('camarasList', (value) => {
                    this.saveDraft();
                    Alpine.store('camaras').update(value);
                    if (this.incidenciaData && this.incidenciaData.detalles) {
                        this.updateMainReportText();
                    }
                }, { deep: true });

                window.addEventListener('auto-distribuir-finalizado', () => {
                    this.handleAutoDistribuir();
                });

                this.$watch('camaraQuery', (value) => {
                    this.camaraIndex = -1;
                    if (value.length < 2) {
                        this.camaraFiltered = [];
                        return;
                    }
                    const q = value.trim().toLowerCase();
                    if (this.tempCamara && this.tempCamara.nombre && this.tempCamara.nombre.toLowerCase() === q) {
                        this.camaraFiltered = [];
                        return;
                    }
                    const results = this.camarasList
                        .filter(c => c.operativa)
                        .map(c => c.name)
                        .filter(name => name.toLowerCase().includes(q));
                    if (results.length === 1 && results[0].toLowerCase() === q) {
                        this.camaraFiltered = [];
                        return;
                    }
                    this.camaraFiltered = results.slice(0, 5);
                });

                // WATCHER PARA DEBUGGEAR CAMBIOS EN horaActual
                this.$watch('horaActual', (newValue, oldValue) => {
                    console.log('⏰ horaActual cambió:', { old: oldValue, new: newValue, desbloqueado: this.camposFechaHoraTurnoDesbloqueados, intervaloActivo: this.intervaloActivo });
                    // Aquí podemos ver la pila de llamadas para identificar quién lo cambió
                    console.trace('Pila de llamadas para cambio de horaActual');
                });

                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('open') === 'halcon') {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('abrir-modal-unidades'));
                        const newUrl = window.location.pathname;
                        window.history.replaceState({}, document.title, newUrl);
                    }, 800);
                }
            },

            saveDraft() {
                if (this.isReceivingExternalUpdate || this.isSubmitting || this.isResettingForm) return;
                this.markUnsaved();
                if (this.saveTimeout) clearTimeout(this.saveTimeout);

                
                this.saveTimeout = setTimeout(() => {
                    this.lastSaved = new Date().getTime();
                    const data = {
                        incidenciaData: JSON.parse(JSON.stringify(this.incidenciaData)),
                        isSaved: this.isSaved,
                        selectedOperadores: JSON.parse(JSON.stringify(this.selectedOperadores)),
                        textAreas: JSON.parse(JSON.stringify(this.textAreas)),
                        supervisor_campo_id: this.supervisor_campo_id,
                        supervisores_camaras: this.supervisores_camaras,
                        selectedCampo: JSON.parse(JSON.stringify(this.selectedCampo)),
                        patrullajeAutomatico: JSON.parse(JSON.stringify(this.patrullajeAutomatico)),
                        patrullandoReportes: JSON.parse(JSON.stringify(this.patrullandoReportes)),
                        camarasReportes: JSON.parse(JSON.stringify(this.camarasReportes)),
                        reportesUnidades: JSON.parse(JSON.stringify(this.reportesUnidades)),
                        turno: this.turno,
                        lastSaved: this.lastSaved,
                        last_modified_by: this.currentUserRole,
                        last_modified_by_name: '{{ Auth::user()->name }}'
                    };
                    localStorage.setItem('reporte_draft', JSON.stringify(data));
                    this.previouslySyncedData = JSON.parse(JSON.stringify(data));
                    // console.log('Borrador guardado (debounced)');

                    // Solo guardar a la BD si soy un usuario estándar o si soy admin en modo de edición colaborativa activa
                    const shouldSaveToDB = this.currentUserRole !== 'admin' || this.isCollaborativeEditing;

                    if (shouldSaveToDB) {
                        axios.post('/api/reportes/draft', {
                            turno: this.turno,
                            fecha: document.getElementById('fecha')?.value || `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-${String(new Date().getDate()).padStart(2, '0')}`,
                            data: data
                        }).then(res => {
                            // console.log('Borrador sincronizado con la BD');
                        }).catch(e => {
                            console.error('Error al sincronizar borrador con BD:', e);
                        });
                    }
                }, 500);
            },

            clearDraft() {
                this.isSubmitting = true;
                if (this.saveTimeout) clearTimeout(this.saveTimeout);
                
                // Limpiar el estado de cambios sin guardar PRIMERO para evitar el alert
                this.clearUnsaved();

                // Inyectar el JSON en los inputs ocultos ANTES de limpiar,
                // para que el POST del formulario lleve los datos correctamente.
                const inputOperadores = document.querySelector('input[name="operadores_camaras"]');
                if (inputOperadores) {
                    inputOperadores.value = JSON.stringify(this.selectedOperadores);
                    console.log('Input operadores value:', inputOperadores.value);
                }

                const inputCampo = document.querySelector('input[name="personal_campo"]');
                if (inputCampo) {
                    inputCampo.value = JSON.stringify(this.selectedCampo.filter(c => c.incluido !== false));
                    console.log('Input campo value:', inputCampo.value);
                }

                const inputUnidades = document.querySelector('input[name="reporte_personal_patrullando"]');
                if (inputUnidades) {
                    inputUnidades.value = JSON.stringify(this.reportesUnidades);
                    console.log('Input unidades value:', inputUnidades.value);
                    console.log('this.reportesUnidades:', this.reportesUnidades);
                }

                const inputVisualizaciones = document.querySelector('input[name="visualizaciones_resaltantes"]');
                if (inputVisualizaciones) {
                    inputVisualizaciones.value = JSON.stringify(this.camarasReportes);
                    console.log('Input visualizaciones value:', inputVisualizaciones.value);
                }

                localStorage.removeItem('reporte_draft');
                localStorage.removeItem('reporte_unidades_draft');
                localStorage.removeItem('report_password_bloqueada');
                localStorage.removeItem('report_intentos_fallidos');
                // No vaciamos this.reportesUnidades ni otras variables aquí para no disparar 
                // watchers de Alpine que puedan encolar otro saveDraft, y porque
                // la página se va a recargar de todos modos tras el submit.

                // Limpiar borrador de la BD
                axios.delete('/api/reportes/draft', {
                    params: {
                        turno: this.turno,
                        fecha: document.getElementById('fecha')?.value || `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-${String(new Date().getDate()).padStart(2, '0')}`
                    }
                }).then(() => {
                    // console.log('Borrador eliminado de la BD');
                }).catch(e => {
                    console.error('Error al limpiar borrador de BD:', e);
                });
            },

            async checkActiveDrafts() {
                try {
                    const response = await axios.get('/api/reportes/draft', {
                        params: {
                            fecha: document.getElementById('fecha')?.value || `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-${String(new Date().getDate()).padStart(2, '0')}`,
                            turno: this.turno
                        }
                    });
                    
                    if (response.data.success) {
                        this.activeDrafts = response.data.drafts;
                    }
                } catch (e) {
                    console.error('Error al verificar borradores activos:', e);
                }
            },

            async startLiveSync(draft, autoRestore = false) {
                this.isLiveSyncing = true;
                this.isCollaborativeEditing = false;
                this.selectedDraftId = draft.id;
                
                // Obtener los datos más frescos inmediatamente
                await this.pollLiveDraft();
                
                // Empezar polling cada 5 segundos
                if (this.syncInterval) clearInterval(this.syncInterval);
                this.syncInterval = setInterval(() => this.pollLiveDraft(), 5000);

                // Registrar inicio de monitoreo en la sesión PHP
                if (!autoRestore) {
                    axios.post('/api/reportes/draft/monitor/start', { user_id: draft.user_id, mode: 'live', draft_id: draft.id })
                        .catch(e => console.error('Error al iniciar sesión de monitoreo:', e));
                    this.triggerNotification(`Monitoreo activado para: ${draft.user_name}`);
                }
            },
            
            stopLiveSync() {
                this.isLiveSyncing = false;
                this.selectedDraftId = null;
                if (this.syncInterval) {
                    clearInterval(this.syncInterval);
                    this.syncInterval = null;
                }
                
                // Registrar fin de monitoreo en la sesión PHP
                axios.post('/api/reportes/draft/monitor/stop')
                    .catch(e => console.error('Error al detener sesión de monitoreo:', e));

                this.isResettingForm = true;
                this.resetForm();
                this.isResettingForm = false;
                this.triggerNotification('Monitoreo desactivado y formulario limpiado');
            },

            async startCollaborativeEdit(draft, autoRestore = false) {
                this.isCollaborativeEditing = true;
                this.isLiveSyncing = false;
                this.selectedDraftId = draft.id;
                
                if (this.syncInterval) {
                    clearInterval(this.syncInterval);
                    this.syncInterval = null;
                }
                
                // Obtener los datos más frescos inmediatamente
                await this.pollLiveDraft();

                // Registrar inicio de monitoreo en la sesión PHP
                if (!autoRestore) {
                    axios.post('/api/reportes/draft/monitor/start', { user_id: draft.user_id, mode: 'collaborative', draft_id: draft.id })
                        .catch(e => console.error('Error al iniciar sesión de monitoreo:', e));
                    this.triggerNotification(`Modo Edición Colaborativa activo para: ${draft.user_name}`);
                }
            },

            stopCollaborativeEdit() {
                this.isCollaborativeEditing = false;
                this.selectedDraftId = null;

                // Registrar fin de monitoreo en la sesión PHP
                axios.post('/api/reportes/draft/monitor/stop')
                    .catch(e => console.error('Error al detener sesión de monitoreo:', e));

                this.isResettingForm = true;
                this.resetForm();
                this.isResettingForm = false;
                this.triggerNotification('Edición colaborativa terminada y formulario limpiado');
            },
            
            async pollLiveDraft() {
                if (!this.selectedDraftId) return;
                try {
                    const response = await axios.get('/api/reportes/draft', {
                        params: {
                            fecha: document.getElementById('fecha')?.value || `${new Date().getFullYear()}-${String(new Date().getMonth() + 1).padStart(2, '0')}-${String(new Date().getDate()).padStart(2, '0')}`,
                            turno: this.turno
                        }
                    });
                    
                    if (response.data.success && response.data.drafts.length > 0) {
                        const currentDraft = response.data.drafts.find(d => d.id === this.selectedDraftId);
                        if (currentDraft) {
                            this.loadDraftData(currentDraft.data);
                        }
                    }
                } catch (e) {
                    console.error('Error al hacer polling del borrador:', e);
                }
            },
            
            loadDraftData(data, isMerge = false) {
                const newHash = this.computeDraftHash(data);
                if (isMerge && newHash && newHash === this.lastProcessedDraftHash) {
                    return;
                }

                this.isReceivingExternalUpdate = true;
                let mergedFieldsCount = 0;
                let conflictCount = 0;

                if (isMerge && this.previouslySyncedData) {
                    // 1. Merge 'incidenciaData'
                    let localInc = this.incidenciaData || { hora: '', detalles: '' };
                    let incomingInc = data.incidenciaData || { hora: '', detalles: '' };
                    let prevInc = this.previouslySyncedData.incidenciaData || { hora: '', detalles: '' };

                    ['hora', 'detalles'].forEach(subKey => {
                        let lVal = localInc[subKey] || '';
                        let iVal = incomingInc[subKey] || '';
                        let pVal = prevInc[subKey] || '';

                        let lChanged = (lVal !== pVal);
                        let iChanged = (iVal !== pVal);

                        if (lChanged && iChanged) {
                            if (lVal !== iVal) {
                                this.incidenciaData[subKey] = iVal;
                                let fieldName = subKey === 'hora' ? 'Hora de Ocurrencia' : 'Detalles de Ocurrencia';
                                this.triggerNotification(`⚠️ Conflicto: El Administrador modificó "${fieldName}". Tu cambio local fue reemplazado.`);
                                conflictCount++;
                                mergedFieldsCount++;
                            }
                        } else if (iChanged) {
                            this.incidenciaData[subKey] = iVal;
                            mergedFieldsCount++;
                        }
                    });

                    // 2. Merge 'textAreas'
                    let localT = this.textAreas || {};
                    let incomingT = data.textAreas || {};
                    let prevT = this.previouslySyncedData.textAreas || {};

                    ['distribucion_personal_camaras', 'distribucion_personal_campo', 'visualizaciones_resaltantes'].forEach(subKey => {
                        let lVal = localT[subKey] || '';
                        let iVal = incomingT[subKey] || '';
                        let pVal = prevT[subKey] || '';

                        let lChanged = (lVal !== pVal);
                        let iChanged = (iVal !== pVal);

                        if (lChanged && iChanged) {
                            if (lVal !== iVal) {
                                this.textAreas[subKey] = iVal;
                                let fieldName = this.getFieldNameFriendly(subKey);
                                this.triggerNotification(`⚠️ Conflicto: El Administrador modificó "${fieldName}". Tu cambio local fue reemplazado.`);
                                conflictCount++;
                                mergedFieldsCount++;
                            }
                        } else if (iChanged) {
                            this.textAreas[subKey] = iVal;
                            mergedFieldsCount++;
                        }
                    });

                    // 3. Merge general keys
                    const generalKeys = [
                        'isSaved',
                        'selectedOperadores',
                        'supervisor_campo_id',
                        'supervisores_camaras',
                        'selectedCampo',
                        'patrullajeAutomatico',
                        'patrullandoReportes',
                        'camarasReportes',
                        'reportesUnidades',
                        'turno'
                    ];

                    generalKeys.forEach(key => {
                        let localVal = this[key];
                        let incomingVal = data[key];
                        let prevVal = this.previouslySyncedData[key];

                        let localChanged = !this.isEqual(localVal, prevVal);
                        let incomingChanged = !this.isEqual(incomingVal, prevVal);

                        if (localChanged && incomingChanged) {
                            if (!this.isEqual(localVal, incomingVal)) {
                                this[key] = this.deepClone(incomingVal);
                                let fieldName = this.getFieldNameFriendly(key);
                                this.triggerNotification(`⚠️ Conflicto: El Administrador modificó "${fieldName}". Tu cambio local fue reemplazado.`);
                                conflictCount++;
                                mergedFieldsCount++;
                            }
                        } else if (incomingChanged) {
                            this[key] = this.deepClone(incomingVal);
                            mergedFieldsCount++;
                        }
                    });

                } else {
                    // Complete overwrite
                    this.incidenciaData = data.incidenciaData || { hora: '', detalles: '' };
                    this.isSaved = data.isSaved || false;
                    this.selectedOperadores = data.selectedOperadores || [];
                    this.textAreas = data.textAreas || {
                        distribucion_personal_camaras: '',
                        distribucion_personal_campo: '',
                        visualizaciones_resaltantes: ''
                    };
                    this.supervisor_campo_id = data.supervisor_campo_id || '';
                    this.supervisores_camaras = data.supervisores_camaras || [];
                    this.selectedCampo = data.selectedCampo || [];
                    this.patrullajeAutomatico = data.patrullajeAutomatico || [];
                    this.patrullandoReportes = data.patrullandoReportes || [];
                    this.camarasReportes = data.camarasReportes || [];
                    this.reportesUnidades = data.reportesUnidades || [];
                }

                // Show/hide textAreas based on contents
                Object.keys(this.textAreas).forEach(key => {
                    const el = document.getElementById(key);
                    if (el) {
                        if (this.textAreas[key] && this.textAreas[key].trim() !== '') {
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }
                    }
                });

                this.updateTomSelectValues();

                // Update local synced snapshot
                this.previouslySyncedData = JSON.parse(JSON.stringify(data));
                this.lastSaved = data.lastSaved || new Date().getTime();
                this.lastProcessedDraftHash = newHash;

                if (isMerge && mergedFieldsCount > 0 && conflictCount === 0) {
                    const changeHash = btoa((newHash || '') + (data.lastSaved || ''));
                    if (data.last_modified_by === 'admin' && changeHash !== this.lastNotifiedChangeHash) {
                        this.triggerNotification('🔄 El administrador ha realizado cambios en el borrador.');
                        this.lastNotifiedChangeHash = changeHash;
                    }
                }

                // Guardar en localStorage para persistencia si el supervisor navega a otra pestaña
                const localData = {
                    incidenciaData: this.incidenciaData,
                    isSaved: this.isSaved,
                    selectedOperadores: this.selectedOperadores,
                    textAreas: this.textAreas,
                    supervisor_campo_id: this.supervisor_campo_id,
                    supervisores_camaras: this.supervisores_camaras,
                    selectedCampo: this.selectedCampo,
                    patrullajeAutomatico: this.patrullajeAutomatico,
                    patrullandoReportes: this.patrullandoReportes,
                    camarasReportes: this.camarasReportes,
                    reportesUnidades: this.reportesUnidades,
                    turno: this.turno,
                    lastSaved: this.lastSaved,
                    last_modified_by: data.last_modified_by,
                    last_modified_by_name: data.last_modified_by_name
                };
                localStorage.setItem('reporte_draft', JSON.stringify(localData));

                setTimeout(() => {
                    this.isReceivingExternalUpdate = false;
                }, 1000);
            },

            isEqual(a, b) {
                if (a === b) return true;
                if (a == null || b == null) return a === b;
                if (typeof a !== typeof b) return false;
                if (typeof a === 'object') {
                    return JSON.stringify(a) === JSON.stringify(b);
                }
                return false;
            },

            deepClone(obj) {
                return obj ? JSON.parse(JSON.stringify(obj)) : null;
            },

            computeDraftHash(draftData) {
                if (!draftData) return null;
                const relevantFields = {
                    incidenciaData: draftData.incidenciaData,
                    selectedOperadores: draftData.selectedOperadores,
                    textAreas: draftData.textAreas,
                    supervisor_campo_id: draftData.supervisor_campo_id,
                    supervisores_camaras: draftData.supervisores_camaras,
                    selectedCampo: draftData.selectedCampo,
                    patrullajeAutomatico: draftData.patrullajeAutomatico,
                    patrullandoReportes: draftData.patrullandoReportes,
                    camarasReportes: draftData.camarasReportes,
                    reportesUnidades: draftData.reportesUnidades,
                    turno: draftData.turno,
                    last_modified_by: draftData.last_modified_by,
                    lastSaved: draftData.lastSaved
                };
                return btoa(unescape(encodeURIComponent(JSON.stringify(relevantFields))));
            },

            getFieldNameFriendly(key) {
                const map = {
                    'distribucion_personal_camaras': 'Distribución de Cámaras',
                    'distribucion_personal_campo': 'Distribución de Personal de Campo',
                    'visualizaciones_resaltantes': 'Visualizaciones Resaltantes',
                    'isSaved': 'Estado de Guardado',
                    'selectedOperadores': 'Operadores Seleccionados',
                    'supervisor_campo_id': 'Supervisor de Campo',
                    'supervisores_camaras': 'Supervisores de Cámaras',
                    'selectedCampo': 'Personal de Campo',
                    'patrullajeAutomatico': 'Patrullaje Automático',
                    'patrullandoReportes': 'Historial de Patrullaje',
                    'camarasReportes': 'Visualizaciones Guardadas',
                    'turno': 'Turno'
                };
                return map[key] || key;
            },

            resetForm() {
                this.incidenciaData = { hora: '', detalles: '' };
                this.isSaved = false;
                this.selectedOperadores = [];
                this.textAreas = {
                    distribucion_personal_camaras: '',
                    distribucion_personal_campo: '',
                    visualizaciones_resaltantes: ''
                };
                this.supervisor_campo_id = '';
                this.supervisores_camaras = [];
                this.selectedCampo = [];
                this.patrullajeAutomatico = [];
                this.patrullandoReportes = [];
                this.camarasReportes = [];
                this.reportesUnidades = [];

                Object.keys(this.textAreas).forEach(key => {
                    const el = document.getElementById(key);
                    if (el) el.classList.add('hidden');
                });

                setTimeout(() => {
                    const supCampoSelect = document.getElementById('supervisor_campo_select');
                    if (supCampoSelect && supCampoSelect.tomselect) {
                        supCampoSelect.tomselect.clear(true);
                    }
                    const supCamarasSelect = document.getElementById('supervisores_camaras_select');
                    if (supCamarasSelect && supCamarasSelect.tomselect) {
                        supCamarasSelect.tomselect.clear(true);
                    }
                }, 100);
            },
            
            updateTomSelectValues() {
                setTimeout(() => {
                    const supCampoSelect = document.getElementById('supervisor_campo_select');
                    if (supCampoSelect && supCampoSelect.tomselect) {
                        supCampoSelect.tomselect.setValue(this.supervisor_campo_id, true);
                    }
                    
                    const supCamarasSelect = document.getElementById('supervisores_camaras_select');
                    if (supCamarasSelect && supCamarasSelect.tomselect) {
                        supCamarasSelect.tomselect.setValue(this.supervisores_camaras, true);
                    }
                }, 100);
            },
            
            saveIncidencia() {
                this.incidenciaData.hora = this.tempHora;
                this.incidenciaData.detalles = this.tempDetalles;
                this.isSaved = true;
                this.showModal = false;
                this.triggerNotification('Incidencia guardada correctamente');
            },

            deleteIncidencia() {
                if (confirm('¿Estás seguro de que deseas eliminar esta incidencia?')) {
                    this.incidenciaData.hora = '';
                    this.incidenciaData.detalles = '';
                    this.tempHora = '';
                    this.tempDetalles = '';
                    this.isSaved = false;
                    this.showModal = false;
                    this.triggerNotification('Incidencia descartada');
                }
            },

            async fetchOperadores() {
                const response = await fetch(`${config.routes.serenazgo_search}?role=Operador de Cámaras&search=${this.operadoresSearch}&activo=1&json=1`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                this.operadoresList = data.map(op => ({
                    ...op,
                    nombres: op.nombres.toUpperCase(),
                    apellido_paterno: op.apellido_paterno.toUpperCase(),
                    apellido_materno: op.apellido_materno ? op.apellido_materno.toUpperCase() : ''
                }));
            },

            toggleOperador(op) {
                const index = this.selectedOperadores.findIndex(s => s.id === op.id);
                if (index > -1) {
                    this.selectedOperadores.splice(index, 1);
                } else {
                    this.selectedOperadores.push({
                        id: op.id,
                        nombres: op.nombres.toUpperCase(),
                        apellido_paterno: op.apellido_paterno.toUpperCase(),
                        apellido_materno: op.apellido_materno ? op.apellido_materno.toUpperCase() : '',
                        perfil_trabajo: op.perfil_trabajo,
                        maquina: '',
                        camaras: [] 
                    });
                }
                this.saveDraft();
            },

            isMaquinaOcupada(maquina, actualOpId) {
                return this.selectedOperadores.some(op => op.maquina === maquina && op.id !== actualOpId);
            },

            isOperadorSelected(id) {
                return this.selectedOperadores.some(s => s.id === id);
            },

            removeOperador(id) {
                this.selectedOperadores = this.selectedOperadores.filter(s => s.id !== id);
                this.saveDraft();
            },

            distribuirCamaras() {
                const operativas = this.camarasList.filter(c => c.operativa);
                if (operativas.length === 0) {
                    this.triggerNotification('No hay cámaras operativas seleccionadas.', 'error');
                    return;
                }
                if (this.selectedOperadores.length === 0) {
                    this.triggerNotification('Primero debes seleccionar a los operadores.', 'info');
                    return;
                }
                
                this.selectedOperadores.forEach(op => op.camaras = []);
                const numOps = this.selectedOperadores.length;
                
                // Separar postes de cámaras regulares - MANTENER ORDEN EXACTO DEL CSV (NO ORDENAR ALFABÉTICAMENTE)
                const isPoste = (name) => name.toUpperCase().includes('POSTE');
                const postes = [...operativas.filter(c => isPoste(c.name))];
                const camarasRegulares = [...operativas.filter(c => !isPoste(c.name))];
                
                const distributeList = (list) => {
                    const baseCount = Math.floor(list.length / numOps);
                    let remainder = list.length % numOps;
                    let currentIndex = 0;
                    this.selectedOperadores.forEach(op => {
                        const countForThisOp = baseCount + (remainder > 0 ? 1 : 0);
                        if (remainder > 0) remainder--;
                        const chunk = list.slice(currentIndex, currentIndex + countForThisOp);
                        op.camaras.push(...chunk.map(c => c.name));
                        currentIndex += countForThisOp;
                    });
                };

                // Distribuir primero las cámaras regulares y luego los postes equitativamente - MANTENIENDO EL ORDEN DEL CSV
                distributeList(camarasRegulares);
                distributeList(postes);

                // NO ORDENAR la lista final - mantener el orden en el que se asignaron (primero cámaras en orden CSV, luego postes en orden CSV)
                // Esto asegura que los primeros postes se asignen primero hasta el final

                this.triggerNotification('Cámaras y postes distribuidos equitativamente entre ' + this.selectedOperadores.length + ' operadores (manteniendo el orden del CSV)');
                this.showCamarasAsignacionModal = false;
                this.saveDraft();
                window.dispatchEvent(new CustomEvent('auto-distribuir-finalizado'));
            },

            toggleCamaraStatus(cam) {
                cam.operativa = !cam.operativa;
                localStorage.setItem('estado_camaras_global', JSON.stringify(this.camarasList.map(c => ({name: c.name, operativa: c.operativa}))));
                this.saveDraft();
                window.dispatchEvent(new CustomEvent('camaras-updated', { detail: { list: this.camarasList } }));
            },

            updateMainReportText() {
                if (!this.incidenciaData || !this.incidenciaData.detalles) return;
                if (!this.incidenciaData.detalles.includes('CÁMARAS OPERATIVAS')) return;
                const total = Alpine.store('camaras').total;
                const postes = Alpine.store('camaras').postes;
                const totalStr = `(${String(total).padStart(2, '0')}) CÁMARAS OPERATIVAS ✅`;
                const postesStr = `(${String(postes).padStart(2, '0')}) POSTES DE EMERGENCIA ACTIVOS 🚨`;
                let lines = this.incidenciaData.detalles.split('\n');
                let changed = false;
                const newLines = lines.map(line => {
                    if (line.includes('CÁMARAS OPERATIVAS')) { changed = true; return totalStr; }
                    if (line.includes('POSTES DE EMERGENCIA ACTIVOS')) { changed = true; return postesStr; }
                    return line;
                });
                if (changed) this.incidenciaData.detalles = newLines.join('\n');
            },

            handleAutoDistribuir() {
                const total = Alpine.store('camaras').total || 0;
                const postes = Alpine.store('camaras').postes || 0;
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

                if (!this.incidenciaData.detalles || this.incidenciaData.detalles.trim() === '' || !this.incidenciaData.detalles.includes('CÁMARAS OPERATIVAS')) {
                    this.incidenciaData.detalles = template;
                    const now = new Date();
                    if (!this.incidenciaData.hora) {
                        this.incidenciaData.hora = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    }
                    this.isSaved = true;
                } else {
                    this.updateMainReportText();
                }
                this.saveDraft();
            },

            handleCampoRecordSaved(detail) {
                const record = detail.record;
                if (record.id) {
                    const index = this.selectedCampo.findIndex(c => c.id === record.id);
                    if (index !== -1) this.selectedCampo[index] = { ...record };
                    this.triggerNotification('Personal de campo actualizado');
                } else {
                    this.selectedCampo.push({ ...record, id: Date.now(), incluido: true });
                }
                this.saveDraft();
            },

            removeCampo(id) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Se eliminará este registro de personal de campo',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.selectedCampo = this.selectedCampo.filter(s => s.id !== id);
                        this.saveDraft();
                        this.triggerNotification('Registro eliminado');
                    }
                });
            },

            syncPatrullaje(campoList) {
                const normalize = (s) => (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                const isHalcon = (c) => c.tipo_patrullaje === 'Vehicular' && /halcon/i.test(normalize(c.descripcion));
                const isCazador = (c) => c.tipo_patrullaje === 'Motorizado' && /cazador/i.test(normalize(c.descripcion));
                const isSierraBravo = (c) => c.tipo_patrullaje === 'A pie' && /sierra bravo/i.test(normalize(c.descripcion));
                const halcones = campoList.filter(isHalcon);
                const cazadores = campoList.filter(isCazador);
                const sierraBravosRaw = campoList.filter(isSierraBravo);
                const sbByNumero = new Map();
                sierraBravosRaw.forEach(c => {
                    const numero = parseInt(c.cantidad, 10);
                    if (!Number.isFinite(numero) || numero <= 0) return;
                    if (!sbByNumero.has(numero)) sbByNumero.set(numero, c);
                });
                const sierraBravos = Array.from(sbByNumero.entries()).sort((a, b) => a[0] - b[0]).map(([, c]) => c);
                const buildHalcon = (unit) => {
                    const existing = this.patrullajeAutomatico.find(p => p.id_campo === unit.id);
                    const personalNames = [unit.chofer, unit.operador, unit.lince].filter(n => n).join(' - ');
                    // Emoji por rango de número de unidad: 1-8 = 🚙 (auto), 9-13 = 🚘 (camioneta)
                    const nroUnidad = parseInt(unit.unidad, 10);
                    let emoji = Number.isFinite(nroUnidad) && nroUnidad >= 9 ? '🚘' : '🚙';
                    const placa = unit.matricula || '';
                    const unidadLimpia = String(unit.unidad).replace(/[🚙🏍️👮🚘]/g, '').trim();
                    const placaLimpia = String(placa).replace(/[🚙🏍️👮🚘]/g, '').trim();
                    const display = `${emoji} ${unidadLimpia} ${placaLimpia}`.trim();
                    return { grupo: 'HALCON', id_campo: unit.id, unidad: unidadLimpia, emoji, placa: placaLimpia, display, personal: personalNames.toUpperCase(), estado: existing ? existing.estado : 'Bien', observacion: existing ? existing.observacion : '', incluido: unit.incluido ?? true };
                };
                const buildCazador = (unit) => {
                    const existing = this.patrullajeAutomatico.find(p => p.id_campo === unit.id);
                    const personalNames = [unit.chofer].filter(n => n).join(' - ');
                    const placa = unit.matricula || (this.motorizadas?.[unit.unidad]?.placa || '');
                    const emoji = '🏍️';
                    const unidadLimpia = String(unit.unidad).replace(/[🚙🏍️👮]/g, '').trim();
                    const placaLimpia = String(placa).replace(/[🚙🏍️👮]/g, '').trim();
                    const display = `${emoji} ${unidadLimpia} ${placaLimpia}`.trim();
                    return { grupo: 'CAZADOR', id_campo: unit.id, unidad: unidadLimpia, emoji, placa: placaLimpia, display, personal: personalNames.toUpperCase(), estado: existing ? existing.estado : 'Bien', observacion: existing ? existing.observacion : '', incluido: unit.incluido ?? true };
                };
                const buildSierraBravo = (unit) => {
                    const numero = parseInt(unit.cantidad, 10);
                    const idCampo = `sb-${numero}`;
                    const existing = this.patrullajeAutomatico.find(p => String(p.id_campo) === String(idCampo));
                    const emoji = '👮';
                    const unidadLimpia = String(numero).replace(/[🚙🏍️👮]/g, '').trim();
                    const display = `${emoji}${unidadLimpia}`;
                    return { grupo: 'SIERRA_BRAVO', id_campo: idCampo, unidad: unidadLimpia, emoji, placa: '', display, personal: '', estado: existing ? existing.estado : 'Bien', observacion: existing ? existing.observacion : '', incluido: unit.incluido ?? true };
                };
                this.patrullajeAutomatico = [ ...halcones.map(buildHalcon), ...cazadores.map(buildCazador), ...sierraBravos.map(buildSierraBravo) ];
            },

            async syncDraftToAPI() {
                if (this.isResettingForm) return;
                const vehicularUnits = this.selectedCampo.filter(c => c.tipo_patrullaje === 'Vehicular' && c.incluido !== false);
                if (vehicularUnits.length === 0) {
                    try { await fetch('/api/draft/clear', { method: 'DELETE', headers: { 'X-CSRF-TOKEN': config.csrf_token, 'Accept': 'application/json' } }); } catch (e) {}
                    return;
                }
                const unidadesParaAPI = vehicularUnits.map(unit => ({ unidad_id: unit.unidad, subtipo: unit.subtipo_vehiculo || 'HALCON', placa: unit.matricula, conductor: [unit.chofer, unit.operador].filter(n => n).join(' - '), sector: unit.ubicacion }));
                try {
                    await fetch('/api/draft/unidades', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': config.csrf_token, 'Accept': 'application/json' }, body: JSON.stringify({ unidades: unidadesParaAPI }) });
                } catch (error) { console.error('Error de red:', error); }
            },

            patrullajeItems(grupo) {
                const items = this.patrullajeAutomatico.filter(p => p.grupo === grupo);
                const num = (v) => { const n = parseInt(v, 10); return Number.isFinite(n) ? n : 9999; };
                return items.sort((a, b) => num(a.unidad) - num(b.unidad));
            },

            patrullajeVista() {
                const order = { 'HALCON': 1, 'CAZADOR': 2, 'SIERRA_BRAVO': 3 };
                const items = [...this.patrullajeAutomatico].sort((a, b) => (order[a.grupo] || 99) - (order[b.grupo] || 99));
                const out = [];
                let lastGrupo = null;
                const titulo = (g) => { if (g === 'HALCON') return 'HALCONES'; if (g === 'CAZADOR') return 'CAZADORES'; if (g === 'SIERRA_BRAVO') return 'SIERRA BRAVO'; return 'UNIDADES'; };
                items.forEach(p => {
                    if (p.grupo !== lastGrupo) { out.push({ _type: 'header', id: `h-${p.grupo}`, label: titulo(p.grupo) }); lastGrupo = p.grupo; }
                    out.push({ _type: 'item', id: `i-${String(p.id_campo)}`, ref: p });
                });
                return out;
            },

            async generateAndCopyReport() {
                const hora = this.horaReporte || (() => { const now = new Date(); return `${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`; })();
                const order = { 'HALCON': 1, 'CAZADOR': 2, 'SIERRA_BRAVO': 3 };
                const seleccionadas = this.patrullajeAutomatico.filter(p => p.incluido).sort((a, b) => (order[a.grupo] || 99) - (order[b.grupo] || 99));
                if (seleccionadas.length === 0) { this.triggerNotification('Por favor selecciona al menos una unidad.', 'warning'); return; }
                let reportText = `*REPORTE DE UNIDADES - ${hora}*\n\n`;
                let lastGrupo = null;
                const titulo = (g) => { if (g === 'HALCON') return '*HALCONES*'; if (g === 'CAZADOR') return '*CAZADORES*'; if (g === 'SIERRA_BRAVO') return '*SIERRA BRAVO*'; return '*UNIDADES*'; };
                seleccionadas.forEach(p => {
                    const obs = (p.observacion || '').trim() || 'SIN NOVEDAD';
                    if (p.grupo !== lastGrupo) { if (lastGrupo !== null) reportText += `\n`; reportText += `${titulo(p.grupo)}\n`; lastGrupo = p.grupo; }
                    reportText += `${p.display}: ${obs.toUpperCase()}\n`;
                });
                if (this.editingReportId) {
                    const index = this.patrullandoReportes.findIndex(r => r.id === this.editingReportId);
                    if (index !== -1) { this.patrullandoReportes[index].detalles = reportText; this.patrullandoReportes[index].unidadesCount = seleccionadas.length; this.patrullandoReportes[index].rawData = JSON.parse(JSON.stringify(this.patrullajeAutomatico)); this.patrullandoReportes[index].hora = hora; }
                    this.editingReportId = null;
                } else {
                    this.patrullandoReportes.unshift({ id: Date.now(), hora: hora, detalles: reportText, unidadesCount: seleccionadas.length, rawData: JSON.parse(JSON.stringify(this.patrullajeAutomatico)) });
                }
                try { await navigator.clipboard.writeText(reportText); this.triggerNotification('Reporte guardado'); } catch (err) { this.triggerNotification('Reporte guardado'); }
                this.showPatrullandoModal = false;
                this.saveDraft();
            },

            loadReport(rep) { window.dispatchEvent(new CustomEvent('abrir-modal-unidades', { detail: rep })); },

            removePatrullando(id) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Este reporte se eliminará del historial',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) { this.patrullandoReportes = this.patrullandoReportes.filter(r => String(r.id) !== String(id)); this.saveDraft(); }
                });
            },

            handleVisualizacionSaved(detail) {
                if (detail.isEdit) {
                    const index = this.camarasReportes.findIndex(r => String(r.id) === String(detail.report.id));
                    if (index !== -1) this.camarasReportes[index] = detail.report;
                } else {
                    this.camarasReportes.unshift(detail.report);
                }
                this.saveDraft();
            },


            removeCamaraReport(id) {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta visualización se eliminará',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) { this.camarasReportes = this.camarasReportes.filter(r => String(r.id) !== String(id)); this.saveDraft(); }
                });
            },
        }
    }

    // Store global para cámaras
    document.addEventListener('alpine:init', () => {
        Alpine.store('camaras', {
            list: [],
            total: 0,
            postes: 0,
            update(newList) {
                this.list = newList;
                this.postes = newList.filter(c => c.operativa && c.name.toUpperCase().includes('POSTE')).length;
                this.total = newList.filter(c => c.operativa && !c.name.toUpperCase().includes('POSTE')).length;
            }
        });
    });

    // Listeners globales
    document.addEventListener('alpine:initialized', () => {
        window.addEventListener('ocurrencia-saved', (event) => {
            const alpineComponent = document.getElementById('reporte-main');
            if (alpineComponent && alpineComponent._x_dataStack) {
                const data = Alpine.$data(alpineComponent);
                data.incidenciaData.hora = event.detail.hora;
                data.incidenciaData.detalles = event.detail.detalle;
                data.isSaved = event.detail.isSaved || true;
                data.triggerNotification('Ocurrencia guardada correctamente');
            }
        });
    });

    // Utilidades (TomSelect, etc.)

    function toggleCategory(id) {
        const textarea = document.getElementById(id);
        if(!textarea) return;
        textarea.classList.toggle('hidden');
        if (!textarea.classList.contains('hidden')) textarea.focus();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const initTomSelect = () => {
            if (document.getElementById('supervisor_campo_select')) {
                let tsCampo = new TomSelect('#supervisor_campo_select', {
                    placeholder: 'Selecciona supervisor...',
                    maxOptions: null,
                    create: false,
                    onChange: function(value) {
                        document.getElementById('supervisor_campo_select').dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                tsCampo.control_input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[0-9]/g, '');
                });
            }
            if (document.getElementById('supervisores_camaras_select')) {
                let tsCamaras = new TomSelect('#supervisores_camaras_select', {
                    plugins: ['remove_button'],
                    placeholder: 'Selecciona supervisores...',
                    maxOptions: null,
                    create: false,
                    onChange: function(value) {
                        document.getElementById('supervisores_camaras_select').dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
                tsCamaras.control_input.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[0-9]/g, '');
                });
            }
        };
        setTimeout(initTomSelect, 100);
    });
</script>
