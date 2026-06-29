<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Seguridad Ciudadana')</title>
    
    <!-- Meta Description -->
    <meta name="description" content="@yield('meta_description', 'Sistema de gestión de reportes, cámaras y megáfonos para seguridad ciudadana.')">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph (OG) -->
    <meta property="og:title" content="@yield('og_title', 'Seguridad Ciudadana - Sistema de Reportes')">
    <meta property="og:description" content="@yield('og_description', 'Sistema de gestión de reportes, cámaras y megáfonos para seguridad ciudadana.')">
    <meta property="og:image" content="@yield('og_image', asset('img/escudo_optimizado.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_PE">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Seguridad Ciudadana - Sistema de Reportes')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Sistema de gestión de reportes, cámaras y megáfonos para seguridad ciudadana.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('img/escudo_optimizado.png'))">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Flatpickr (Calendario) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Custom scrollbar styles */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 3px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        
        /* Firefox */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #94a3b8 #f1f5f9;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        /* Estilos para los atajos de teclado */
        #sidebar u {
            text-decoration: none;
            border-bottom: 1.5px solid currentColor;
            display: inline-block;
            line-height: 1;
            padding-bottom: 0px;
            opacity: 0.9;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen overflow-x-hidden" x-data="{ sidebarOpen: true }">
    <div class="flex">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-h-screen overflow-hidden">
            <!-- Header -->
            @include('layouts.partials.header')

            <!-- Main Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 animate-fade-in">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Modal Flotante: Buscador de Megáfonos -->
    <div x-data="{
        megafonos: [],
        cargando: false,
        filtro: '',
        seleccionado: null,
        abierto: false,
        minimizado: false,
        
        init() {
            // Cargar datos dinámicos
            this.cargando = true;
            fetch('{{ route("api.megafonos") }}')
                .then(res => res.json())
                .then(data => {
                    this.megafonos = data;
                    this.cargando = false;
                })
                .catch(e => {
                    console.error('Error cargando megáfonos:', e);
                    this.cargando = false;
                });

            // Cargar estado inicial desde localStorage
            const savedAbierto = localStorage.getItem('megafono_abierto');
            if (savedAbierto !== null) {
                this.abierto = savedAbierto === 'true';
            }
            
            const savedMinimizado = localStorage.getItem('megafono_minimizado');
            if (savedMinimizado !== null) {
                this.minimizado = savedMinimizado === 'true';
            }
            
            // Vigilar cambios para persistir
            this.$watch('abierto', val => localStorage.setItem('megafono_abierto', val));
            this.$watch('minimizado', val => localStorage.setItem('megafono_minimizado', val));
        },
        
        get filtrados() {
            if (!this.filtro) return this.megafonos;
            const busqueda = this.filtro.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            return this.megafonos.filter(m => {
                const nombre = m.nombre.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                return nombre.includes(busqueda);
            });
        },
        
        seleccionar(megafono) {
            this.seleccionado = megafono;
        },
        
        limpiar() {
            this.filtro = '';
            this.seleccionado = null;
        },
        
        toggleMinimizar() {
            this.minimizado = !this.minimizado;
        },
        
        cerrar() {
            this.abierto = false;
            this.minimizado = false;
            this.limpiar();
        }
    }" 
    @toggle-megafono.window="abierto = true; minimizado = false;"
    x-show="abierto"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed z-50"
    :class="minimizado ? 'bottom-4 right-4' : 'bottom-4 right-4 w-96'"
    style="max-height: 80vh;">
    
    <!-- Vista minimizada -->
    <div x-show="minimizado" @click="minimizado = false" class="bg-blue-600 text-white p-3 rounded-full shadow-lg cursor-pointer hover:bg-blue-700 transition-colors flex items-center justify-center w-12 h-12">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
        </svg>
    </div>
    
    <!-- Vista expandida -->
    <div x-show="!minimizado" class="bg-white rounded-xl shadow-2xl border border-gray-200 flex flex-col">
        <!-- Header Banner Estilo Custom -->
        <div class="bg-blue-600 rounded-t-xl py-5 px-4 relative flex items-center min-h-[110px]">
            <!-- Personaje sobresaliente mucho más grande -->
            <img src="/img/logo_megafonos.png" alt="Megafonos" class="absolute -bottom-[2px] -left-6 w-[180px] h-[180px] object-contain drop-shadow-lg z-10 pointer-events-none">
            
            <div class="ml-[150px] flex-1 pr-4">
                <h2 class="text-white font-black text-xl leading-tight tracking-tight drop-shadow-md">BUSCADOR DE<br>MEGÁFONOS</h2>
                <p class="text-blue-50 text-[10px] mt-1 leading-snug drop-shadow-sm font-medium">
                    Encuentra y gestiona los megáfonos instalados en tu jurisdicción.
                </p>
                <div class="mt-2 inline-flex items-center bg-white rounded-full px-2.5 py-0.5 shadow-sm">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                    <span class="text-green-700 text-[9px] font-black tracking-wider">SISTEMA EN LÍNEA</span>
                </div>
            </div>

            <!-- Botones de control flotantes arriba a la derecha -->
            <div class="absolute top-2 right-2 flex items-center space-x-1 z-20">
                <button @click="toggleMinimizar()" class="p-1 text-white hover:bg-white hover:bg-opacity-20 rounded transition-colors" title="Minimizar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <button @click="cerrar()" class="p-1 text-white hover:bg-red-500 rounded transition-colors" title="Cerrar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
        
        <!-- Búsqueda -->
        <div class="p-3 border-b border-gray-200">
            <div class="relative">
                <input 
                    type="text" 
                    x-model="filtro" 
                    placeholder="Buscar por nombre..." 
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    @keyup.esc="limpiar()">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button x-show="filtro" @click="limpiar()" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Resultado seleccionado -->
        <div x-show="seleccionado" class="bg-blue-50 p-3 border-b border-blue-100">
            <div class="text-xs text-blue-600 font-semibold uppercase tracking-wide mb-1">Código Seleccionado</div>
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-bold text-gray-800 text-sm" x-text="seleccionado?.nombre"></div>
                    <div class="text-lg font-bold text-blue-600" x-text="seleccionado?.codigo"></div>
                </div>
                <button @click="seleccionado = null" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Lista de resultados -->
        <div class="max-h-64 overflow-y-auto p-2">
            <template x-for="m in filtrados" :key="m.nombre">
                <div 
                    @click="seleccionar(m)"
                    class="flex items-center justify-between p-2 rounded-lg cursor-pointer transition-colors mb-1"
                    :class="seleccionado?.nombre === m.nombre ? 'bg-blue-100 border border-blue-200' : 'hover:bg-gray-100 border border-transparent'">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700" x-text="m.nombre"></span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span 
                            class="text-xs font-bold px-2 py-0.5 rounded"
                            :class="m.codigo === '***' ? 'bg-gray-200 text-gray-600' : 'bg-blue-100 text-blue-700'"
                            x-text="m.codigo"></span>
                    </div>
                </div>
            </template>
            <div x-show="filtrados.length === 0" class="text-center py-4 text-gray-500 text-sm">
                No se encontraron resultados
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 p-2 border-t border-gray-200 flex justify-between items-center">
            <button @click="limpiar()" class="text-xs text-gray-600 hover:text-gray-800 flex items-center space-x-1 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Limpiar</span>
            </button>
            <div class="text-xs text-gray-500 font-medium px-2">
                Total: <span x-text="filtrados.length"></span> ubicaciones
            </div>
        </div>
    </div>
</div>

    <!-- Script para sidebar colapsable -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                open: true,
                toggle() {
                    this.open = !this.open;
                }
            }));
        });
    </script>
    
    <!-- Sistema de Notificaciones Global -->
    <div x-data="notificationService()" x-init="init()"></div>

    <script>
        function notificationService() {
            return {
                config: @json($global_shift_settings),
                
                init() {
                    console.log('Servicio de Notificaciones Iniciado');
                    this.checkTime();
                    // Revisar cada 10 segundos
                    setInterval(() => this.checkTime(), 10000);

                    // Solicitar permiso para notificaciones de escritorio (más confiable en segundo plano)
                    if ('Notification' in window && Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                        Notification.requestPermission();
                    }
                },
                
                checkTime() {
                    const now = new Date();
                    const dateStr = now.toISOString().split('T')[0];
                    const hours = now.getHours().toString().padStart(2, '0');
                    const minutes = now.getMinutes().toString().padStart(2, '0');
                    const currentTime = `${hours}:${minutes}`;
                    
                    let currentShift = '';
                    const h = parseInt(hours);
                    if (h >= 6 && h < 14) currentShift = 'DIA';
                    else if (h >= 14 && h < 22) currentShift = 'TARDE';
                    else currentShift = 'NOCHE';
                    
                    const shiftConfig = this.config[currentShift];
                    if (!shiftConfig) return;
                    
                    if (shiftConfig.notifications.includes(currentTime)) {
                        const notificationKey = `notified_${dateStr}_${currentTime}`;
                        // Solo mostrar si no se ha notificado y NO hay ya un modal abierto (para evitar que se trabe)
                        if (!localStorage.getItem(notificationKey)) {
                            if (!Swal.isVisible()) {
                                this.showReportPopup();
                                this.showDesktopNotification(currentTime);
                                localStorage.setItem(notificationKey, 'true');
                                this.playAlertSound();
                            }
                        }
                    }
                },

                showDesktopNotification(time) {
                    if ('Notification' in window && Notification.permission === 'granted') {
                        new Notification('SISTEMA DE REPORTES', {
                            body: `¡Es momento del reporte de las ${time}! Haz clic para empezar.`,
                            icon: '/img/escudo_optimizado.png',
                            tag: 'reporte-alerta'
                        }).onclick = () => {
                            window.focus();
                            if (window.location.pathname === '{{ route('reportes.nuevo', [], false) }}') {
                                window.dispatchEvent(new CustomEvent('abrir-modal-unidades'));
                            } else {
                                window.location.href = '{{ route('reportes.nuevo') }}?open=halcon';
                            }
                        };
                    }
                },

                playAlertSound() {
                    try {
                        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                        audio.play().catch(e => console.log('Audio bloqueado por el navegador.'));
                    } catch (e) {
                        console.error('Error al reproducir sonido:', e);
                    }
                },
                
                showReportPopup() {
                    Swal.fire({
                        title: '<span class="text-slate-800 tracking-tight font-black uppercase">Sistema de Reportes</span>',
                        html: `
                            <div class="py-6">
                                <h3 class="text-2xl font-black text-blue-900 mb-2">¡ES EL MOMENTO DEL REPORTE!</h3>
                                <p class="text-lg font-bold text-blue-800 opacity-70">¿QUIERES TOMARLO AHORA?</p>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'SÍ, EMPEZAR',
                        cancelButtonText: 'NO, LUEGO',
                        confirmButtonColor: '#22c55e',
                        cancelButtonColor: '#ef4444',
                        reverseButtons: true,
                        timer: 45000,
                        timerProgressBar: true,
                        background: '#ffffff',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'rounded-[40px] border-none shadow-2xl',
                            confirmButton: 'rounded-2xl px-8 py-4 font-black uppercase text-xs tracking-widest shadow-lg shadow-emerald-100',
                            cancelButton: 'rounded-2xl px-8 py-4 font-black uppercase text-xs tracking-widest'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (window.location.pathname === '{{ route('reportes.nuevo', [], false) }}') {
                                window.dispatchEvent(new CustomEvent('abrir-modal-unidades'));
                            } else {
                                window.location.href = '{{ route('reportes.nuevo') }}?open=halcon';
                            }
                        }
                    });
                }
            }
        }
    </script>
    
    <script>
        // Manejador global de atajos de teclado (Alt + Tecla)
        document.addEventListener('keydown', (e) => {
            // Solo actuar si Alt está presionado y no otras teclas modificadoras
            if (e.altKey && !e.ctrlKey && !e.metaKey) {
                const key = e.key.toLowerCase();
                const target = document.querySelector(`[accesskey="${key}"]`);
                
                if (target) {
                    e.preventDefault();
                    // Si es un botón de logout, enviamos el form
                    if (target.type === 'submit' && target.closest('form')) {
                        target.closest('form').submit();
                    } else {
                        target.click();
                    }
                }
            }
        });
    </script>
    
    <!-- Global Notification System -->
    <div x-data="{
        notifications: [],
        add(e) {
            const id = Date.now();
            this.notifications.push({
                id: id,
                message: e.detail.message,
                type: e.detail.type || 'success'
            });
            setTimeout(() => this.remove(id), 3000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }" 
    @notify.window="add($event)" 
    class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-[100] flex flex-col items-center space-y-2 pointer-events-none"
    x-cloak>
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 :class="{
                     'bg-slate-800 text-white shadow-xl': notification.type === 'success',
                     'bg-red-500 text-white shadow-xl': notification.type === 'error',
                     'bg-amber-500 text-white shadow-xl': notification.type === 'warning',
                     'bg-blue-500 text-white shadow-xl': notification.type === 'info'
                 }"
                 class="px-6 py-3 rounded-full font-medium text-sm flex items-center space-x-3 pointer-events-auto max-w-md text-center">
                
                <!-- Icons based on type -->
                <template x-if="notification.type === 'success'">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
                <template x-if="notification.type === 'error'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
                <template x-if="notification.type === 'warning'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </template>
                <template x-if="notification.type === 'info'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </template>
                
                <span x-text="notification.message"></span>
            </div>
        </template>
    </div>

    <!-- Laravel Flash Messages to Alpine Notification -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('status'))
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ session('status') }}", type: 'success' }}));
            @endif

            @if(session('error'))
                window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ session('error') }}", type: 'error' }}));
            @endif

            @if($errors->any())
                @foreach($errors->all() as $error)
                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: "{{ $error }}", type: 'error' }}));
                @endforeach
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>
