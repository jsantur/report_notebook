<div x-show="sidebarOpen" x-transition:enter="transition-all ease-in-out duration-300" x-transition:enter-start="-translate-x-full w-0 opacity-0" x-transition:enter-end="translate-x-0 w-64 opacity-100" x-transition:leave="transition-all ease-in-out duration-300" x-transition:leave-start="translate-x-0 w-64 opacity-100" x-transition:leave-end="-translate-x-full w-0 opacity-0" :class="sidebarOpen ? 'w-64 relative' : 'w-0 fixed -translate-x-full'" class="bg-[#0051A1] text-white overflow-hidden flex-shrink-0 border-r border-white/5" id="sidebar">
    <!-- Botón para colapsar sidebar -->
    <button @click="sidebarOpen = false" class="absolute top-4 right-4 p-2 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors z-50" title="Colapsar menú">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
    </button>

    <!-- Perfil de Usuario -->
    <div class="p-8 mt-4 flex flex-col items-center text-center">
        <div class="relative inline-block group mb-4">
            <div class="w-20 h-20 rounded-full border-2 border-white p-1 shadow-[0_0_20px_rgba(255,255,255,0.2)] transition-transform duration-500 group-hover:scale-105">
                <div class="w-full h-full bg-slate-800 rounded-full flex items-center justify-center font-black text-2xl text-white">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <div class="absolute bottom-1 right-1 w-5 h-5 bg-[#0051A1] rounded-full flex items-center justify-center border-2 border-[#0051A1]">
                <div class="w-3 h-3 bg-white rounded-full animate-pulse shadow-[0_0_10px_rgba(255,255,255,0.8)]"></div>
            </div>
        </div>
        <div class="w-full px-2">
            <p class="text-[10px] font-black text-white/60 uppercase tracking-widest mb-1">
                {{ Auth::user()->role === 'admin' ? 'Administrador:' : 'Usuario estándar:' }}
            </p>
            <p class="text-xs font-black text-white uppercase tracking-tight leading-tight whitespace-normal break-words">
                {{ Auth::user()->name }}
            </p>
            <div class="flex items-center justify-center mt-3">
                <span class="px-2 py-0.5 bg-white/10 border border-white/20 rounded-full text-[9px] font-black text-white uppercase tracking-tighter">Online</span>
            </div>
        </div>
    </div>

    <nav class="mt-4 px-4 space-y-1.5 pb-10">
        <a href="{{ route('dashboard') }}" accesskey="i" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>I</u>nicio</span>
        </a>

        <a href="{{ route('reportes.nuevo') }}" accesskey="n" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('reportes.nuevo') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('reportes.nuevo') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>N</u>uevo Reporte</span>
        </a>

        <a href="{{ route('kilometrajes.index') }}" accesskey="k" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('kilometrajes.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('kilometrajes.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17h2l.62-3.41A2 2 0 0019.65 11H18M5 17H3l-.62-3.41A2 2 0 014.35 11H6m12 0V9a2 2 0 00-2-2H8a2 2 0 00-2 2v2m12 0H6M9 17a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>K</u>ilometrajes</span>
        </a>

        <button @click="$dispatch('toggle-megafono')" accesskey="m" class="w-full group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 text-blue-100/70 hover:bg-white/10 hover:text-white">
            <svg class="w-5 h-5 text-white/80 group-hover:scale-110 group-hover:text-white transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>M</u>egáfonos</span>
        </button>

        <a href="{{ route('reportes.index') }}" accesskey="b" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('reportes.index') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('reportes.index') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>B</u>uscar Reporte</span>
        </a>

        @if(Auth::user()->role === 'admin')
        <div class="py-4 px-3">
            <div class="h-px bg-white/10 w-full"></div>
        </div>

        <a href="{{ route('serenazgo.index') }}" accesskey="s" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('serenazgo.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('serenazgo.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>S</u>erenazgo</span>
        </a>

        <a href="{{ route('usuarios.index') }}" accesskey="u" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('usuarios.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('usuarios.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>U</u>suarios</span>
        </a>

        <a href="{{ route('vehiculos.index') }}" accesskey="v" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('vehiculos.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('vehiculos.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 17h2l.62-3.41A2 2 0 0019.65 11H18M5 17H3l-.62-3.41A2 2 0 014.35 11H6m12 0V9a2 2 0 00-2-2H8a2 2 0 00-2 2v2m12 0H6M9 17a2 2 0 100-4 2 2 0 000 4zm6 0a2 2 0 100-4 2 2 0 000 4z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>V</u>ehículos</span>
        </a>

        {{-- 
        <a href="{{ route('camaras.index') }}" accesskey="c" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('camaras.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('camaras.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide"><u>C</u>ámaras</span>
        </a>
        --}}

        <a href="{{ route('backups.index') }}" accesskey="a" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('backups.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('backups.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide">B<u>a</u>ckups</span>
        </a>

        <a href="{{ route('configuracion.index') }}" accesskey="g" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 {{ request()->routeIs('configuracion.*') ? 'bg-blue-400/30 text-white border-l-4 border-white shadow-lg' : 'text-blue-100/70 hover:bg-white/10 hover:text-white' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('configuracion.*') ? 'text-white' : 'text-white/80 group-hover:scale-110 group-hover:text-white transition-all' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide">Confi<u>g</u>uración</span>
        </a>
        @endif

        <div class="py-2 px-3">
            <div class="h-px bg-white/10 w-full"></div>
        </div>

        <a href="{{ route('manual') }}" target="_blank" class="group flex items-center space-x-3 p-3.5 rounded-xl transition-all duration-300 text-blue-100/70 hover:bg-white/10 hover:text-white">
            <svg class="w-5 h-5 text-white animate-pulse group-hover:scale-110 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="text-xs font-bold tracking-wide">Manual de Usuario</span>
        </a>

        <div class="pt-6">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit" accesskey="x" class="w-full group flex items-center space-x-3 p-3.5 rounded-xl bg-white/5 hover:bg-rose-600 transition-all duration-300">
                    <svg class="w-5 h-5 text-rose-400 group-hover:text-white group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white/90 group-hover:text-white">Cerrar sesión (<u>X</u>)</span>
                </button>
            </form>
        </div>
    </nav>
</div>
