<header x-data="{ openAboutModal: false }" @keydown.window.f1.prevent="openAboutModal = true" @keydown.window.escape="openAboutModal = false" class="bg-white h-20 flex items-center justify-between px-8 relative z-[100] border-b border-slate-100">
    <!-- Izquierda -->
    <div class="flex items-center w-1/4">
        <button @click="sidebarOpen = true" x-show="!sidebarOpen" class="p-2.5 rounded-xl bg-slate-50 text-slate-600 hover:bg-slate-100 transition-all mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Centro -->
    <div class="flex-1 text-center">
        <h1 class="text-sm md:text-base font-bold text-slate-800 tracking-tight flex items-center justify-center uppercase">
            <span class="bg-indigo-600 w-2 h-2 rounded-full mr-3"></span>
            Gerencia de Seguridad Ciudadana MTP 
            <span class="ml-2 text-slate-400 font-medium lowercase">| Panel administrativo</span>
            <button @click="openAboutModal = true" class="ml-3 text-slate-400 hover:text-indigo-600 transition-colors focus:outline-none" title="Acerca de (F1)">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
        </h1>
    </div>

    <!-- Derecha (Vacio para balance) -->
    <div class="w-1/4 flex justify-end">
    </div>

    <!-- About Modal -->
    <div x-show="openAboutModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[200] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="openAboutModal = false" 
             x-show="openAboutModal"
             x-transition:enter="transition ease-out duration-300 delay-75"
             x-transition:enter-start="opacity-0 translate-y-8 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-8 scale-95"
             class="bg-white rounded-3xl shadow-2xl overflow-hidden max-w-lg w-full transform p-6 relative m-4">
            <button @click="openAboutModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 bg-slate-100 rounded-full p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="text-center">
                <img src="{{ asset('img/Presentacion.jpeg') }}" alt="Presentación" class="w-full h-auto rounded-2xl mb-6 shadow-md object-cover">
                <h2 class="text-2xl font-black text-slate-800 mb-2">Proyecto Marte v1.0</h2>
                <p class="text-slate-500 mb-6 font-medium">Desarrollado por Joseph Santur M. ヾ(⌐■_■)ノ</p>
                <a href="https://wa.me/51916582265?text={{ urlencode('Me estoy contactando desde Proyecto Marte v1.0 - Joseph Santur M. ヾ(⌐■_■)ノ quiero hablar contigo...') }}" target="_blank" class="inline-flex items-center justify-center w-full bg-[#25D366] hover:bg-[#128C7E] text-white font-bold py-4 px-6 rounded-2xl shadow-lg shadow-green-200 transition-all hover:-translate-y-0.5">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564c.173.087.289.129.332.202.043.073.043.423-.101.827z"/></svg>
                    Contactar por WhatsApp
                </a>
            </div>
        </div>
    </div>
</header>
