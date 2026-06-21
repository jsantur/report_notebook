@extends('layouts.app')

@section('title', 'Registrar Vehículos - Seguridad Ciudadana')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Cabecera Premium (Estilo Kilometrajes) -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <!-- Izquierda: Identidad -->
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3.5 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Parque Automotor</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Registro y control de unidades móviles</p>
            </div>
        </div>

        <!-- Derecha: Acciones -->
        <div class="flex items-center space-x-4 w-full lg:w-auto">
            <button type="submit" form="registrationForm" id="submitButton" class="w-full lg:w-auto px-10 py-4 bg-blue-600 text-white rounded-[24px] font-black text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                REGISTRAR VEHÍCULO
            </button>
        </div>
    </div>

    <!-- Formulario de Registro -->
    <div class="bg-white rounded-b-xl p-8 mb-10 shadow-xl border-x border-b border-gray-100">
        <form id="registrationForm" action="{{ route('vehiculos.store') }}" method="POST" class="space-y-6">
            @csrf
            <div id="methodField"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipo de Vehículo -->
                <div class="relative border-b border-gray-300">
                    <select name="tipo" id="tipo" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Tipo de Vehículo</option>
                        <option value="CAMIONETA">CAMIONETA</option>
                        <option value="AUTO">AUTO</option>
                        <option value="MOTO LINEAL">MOTO LINEAL</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>

                <!-- Placa -->
                <div class="relative border-b border-gray-300">
                    <input type="text" name="placa" id="placa" placeholder="Placa (ej. ABC-123)" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('placa') }}" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nro de Unidad -->
                <div class="relative border-b border-gray-300">
                    <input type="text" name="nro_unidad" id="nro_unidad" placeholder="Nro de Unidad (ej. 01)" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('nro_unidad') }}" required>
                </div>

                <!-- Tipo de Patrullaje -->
                <div class="relative border-b border-gray-300">
                    <select name="tipo_patrullaje" id="tipo_patrullaje" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Tipo de Patrullaje</option>
                        <option value="Vehicular">Vehicular</option>
                        <option value="Motorizado">Motorizado</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <div class="relative border-b border-gray-300">
                <input type="text" name="descripcion" id="descripcion" placeholder="Descripción (Opcional)" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('descripcion') }}">
            </div>

            <!-- Botón Cancelar -->
            <div id="cancelContainer" class="hidden pt-4">
                <button type="button" onclick="resetForm()" class="w-full bg-gray-400 text-white py-2 rounded-lg font-semibold hover:bg-gray-500 transition-colors shadow-sm">
                    Cancelar Edición
                </button>
            </div>
        </form>
    </div>

    <!-- Buscador -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <h3 class="text-xl font-bold text-gray-800">Vehículos Registrados:</h3>
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" id="searchInput" placeholder="Buscar (Placa, Unidad o Tipo)" class="block w-full pl-10 pr-3 py-2 border border-transparent bg-gray-100 rounded-lg focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all text-sm">
        </div>
    </div>

    <!-- Listado de Vehículos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <div class="max-h-[500px] overflow-y-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Placa</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Tipo</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Unidad</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Patrullaje</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Estado</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="vehicleList" class="divide-y divide-gray-50">
                        @include('vehiculos.partials.list')
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginación -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100" id="paginationLinks">
            {{ $vehiculos->links() }}
        </div>
    </div>
</div>

@if(session('status'))
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-bounce">
    {{ session('status') }}
</div>
@endif

@if ($errors->any())
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@endsection

@push('scripts')
<script>
    const form = document.getElementById('registrationForm');
    const submitButton = document.getElementById('submitButton');
    const cancelContainer = document.getElementById('cancelContainer');
    const methodField = document.getElementById('methodField');
    const originalAction = form.action;

    function editVehiculo(v) {
        // Llenar campos
        document.getElementById('tipo').value = v.tipo;
        document.getElementById('placa').value = v.placa;
        document.getElementById('nro_unidad').value = v.nro_unidad;
        document.getElementById('tipo_patrullaje').value = v.tipo_patrullaje;
        document.getElementById('descripcion').value = v.descripcion || '';

        // Cambiar estado visual
        submitButton.innerText = 'GUARDAR CAMBIOS';
        cancelContainer.classList.remove('hidden');
        
        // Cambiar action del form y método
        form.action = `/vehiculos/${v.id}`;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Scroll suave al formulario
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetForm() {
         form.reset();
         form.action = originalAction;
         methodField.innerHTML = '';
         submitButton.innerText = 'REGISTRAR';
         cancelContainer.classList.add('hidden');
     }

      // Lógica de Búsqueda AJAX en tiempo real
      const searchInput = document.getElementById('searchInput');
      const vehicleList = document.getElementById('vehicleList');
      const paginationLinks = document.getElementById('paginationLinks');
      let timeout = null;

      searchInput.addEventListener('keyup', function() {
          clearTimeout(timeout);
          timeout = setTimeout(() => {
              const query = searchInput.value;
              
              if (query.length > 0) {
                  paginationLinks.classList.add('hidden');
              } else {
                  paginationLinks.classList.remove('hidden');
              }

              fetch(`{{ route('vehiculos.index') }}?search=${query}`, {
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest'
                  }
              })
              .then(response => response.text())
              .then(html => {
                  vehicleList.innerHTML = html;
              })
              .catch(error => console.error('Error en la búsqueda:', error));
          }, 300);
      });

      function confirmDelete(id) {
          Swal.fire({
              title: '¿Estás seguro?',
              text: "¡No podrás revertir esto!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#2563eb',
              cancelButtonColor: '#ef4444',
              confirmButtonText: 'Sí, eliminar',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (result.isConfirmed) {
                  document.getElementById('delete-form-' + id).submit();
              }
          });
      }
</script>
@endpush
