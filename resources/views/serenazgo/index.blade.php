@extends('layouts.app')

@section('title', 'Registrar Personal Serenazgo - Seguridad Ciudadana')

@section('content')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
<div class="max-w-7xl mx-auto">
    <!-- Cabecera Premium (Estilo Kilometrajes) -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-8 border border-gray-100 flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
        <!-- Izquierda: Identidad -->
        <div class="flex items-center space-x-4">
            <div class="bg-blue-600 p-3.5 rounded-2xl text-white shadow-lg shadow-blue-100">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">Personal Serenazgo</h2>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Registro y gestión del equipo operativo</p>
            </div>
        </div>

        <!-- Derecha: Acciones -->
        <div class="flex items-center space-x-4 w-full lg:w-auto">
            <button type="submit" form="registrationForm" id="submitButton" class="w-full lg:w-auto px-10 py-4 bg-blue-600 text-white rounded-[24px] font-black text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                REGISTRAR PERSONAL
            </button>
        </div>
    </div>

    <!-- Formulario de Registro -->
    <div class="bg-white rounded-b-xl p-8 mb-10 shadow-xl border-x border-b border-gray-100">
        <form id="registrationForm" action="{{ route('serenazgo.store') }}" method="POST" class="space-y-6">
            @csrf
            <div id="methodField"></div>
            <!-- Fila 1 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative border-b border-gray-300">
                    <input type="text" name="nombres" id="nombres" placeholder="Nombres" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('nombres') }}" required>
                </div>
                <div class="relative border-b border-gray-300">
                    <input type="text" name="apellido_paterno" id="apellido_paterno" placeholder="Apellido Paterno" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('apellido_paterno') }}" required>
                </div>
                <div class="relative border-b border-gray-300">
                    <input type="text" name="apellido_materno" id="apellido_materno" placeholder="Apellido Materno" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('apellido_materno') }}" required>
                </div>
            </div>

            <!-- Fila 2 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="relative border-b border-gray-300 flex items-center">
                    <input type="text" name="dni" id="dni" placeholder="DNI (8 dígitos)" class="w-full bg-transparent py-2 pr-10 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('dni') }}" maxlength="8" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    <button type="button" onclick="consultarDniSerenazgo()" class="absolute right-0 p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors group" title="Autocompletar nombres y apellidos usando el DNI (Graph Perú)">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                    </button>
                </div>
                <div class="relative border-b border-gray-300">
                    <input type="text" name="fecha_nacimiento" id="fecha_nacimiento" placeholder="Fecha de Nacimiento (AAAA-MM-DD)" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('fecha_nacimiento') }}" required>
                </div>
                <div class="relative border-b border-gray-300">
                    <input type="text" name="celular" id="celular" placeholder="Celular (opcional)" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('celular') }}" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
            </div>

            <!-- Fila 3 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="relative border-b border-gray-300">
                    <select name="perfil_trabajo" id="perfil_trabajo" required class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors appearance-none cursor-pointer">
                        <option value="" disabled selected>Perfil de Trabajo</option>
                        <option value="Supervisor Encargado" {{ old('perfil_trabajo') == 'Supervisor Encargado' ? 'selected' : '' }}>Supervisor Encargado</option>
                        <option value="Chofer" {{ old('perfil_trabajo') == 'Chofer' ? 'selected' : '' }}>Chofer</option>
                        <option value="Operador de Cámaras" {{ old('perfil_trabajo') == 'Operador de Cámaras' ? 'selected' : '' }}>Operador de Cámaras</option>
                        <option value="Sereno" {{ old('perfil_trabajo') == 'Sereno' ? 'selected' : '' }}>Sereno</option>
                        <option value="Supervisor de Cámaras" {{ old('perfil_trabajo') == 'Supervisor de Cámaras' ? 'selected' : '' }}>Supervisor de Cámaras</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </div>
                <div class="relative border-b border-gray-300">
                    <input type="text" name="nombre_foto" id="nombre_foto" placeholder="Nombre de la Foto (ej. foto1.jpg)" class="w-full bg-transparent py-2 focus:outline-none focus:border-blue-500 transition-colors" value="{{ old('nombre_foto') }}">
                </div>
            </div>

            <!-- Botón Cancelar (Oculto inicialmente) -->
            <div id="cancelContainer" class="hidden pt-4">
                <button type="button" onclick="resetForm()" class="w-full bg-gray-400 text-white py-2 rounded-lg font-semibold hover:bg-gray-500 transition-colors shadow-sm">
                    Cancelar Edición
                </button>
            </div>
        </form>
    </div>

    <!-- Buscador -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
        <h3 class="text-xl font-bold text-gray-800">Personal Registrado:</h3>
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input type="text" id="searchInput" placeholder="Buscar (Apellidos, Puesto o DNI)" class="block w-full pl-10 pr-3 py-2 border border-transparent bg-gray-100 rounded-lg focus:outline-none focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all text-sm">
        </div>
    </div>

    <!-- Listado de Personal -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <div class="max-h-[500px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">DNI</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Nombre Completo</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Celular</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Fecha Nac.</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100">Puesto</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider border-b border-gray-100 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="personnelList" class="divide-y divide-gray-50">
                        @include('serenazgo.partials.list')
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Paginación -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100" id="paginationLinks">
            {{ $personnel->links() }}
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

    function editPerson(person) {
        // Llenar campos
        document.getElementById('nombres').value = person.nombres;
        document.getElementById('apellido_paterno').value = person.apellido_paterno;
        document.getElementById('apellido_materno').value = person.apellido_materno;
        document.getElementById('dni').value = person.dni;
        // Cargar fecha en Flatpickr si existe
        if (person.fecha_nacimiento && fp) {
            fp.setDate(person.fecha_nacimiento);
        }
        document.getElementById('celular').value = person.celular;
        document.getElementById('perfil_trabajo').value = person.perfil_trabajo;
        document.getElementById('nombre_foto').value = person.nombre_foto || '';

        // Cambiar estado visual
        submitButton.innerText = 'Guardar Cambios';
        cancelContainer.classList.remove('hidden');
        
        // Cambiar action del form y método
        form.action = `/serenazgo/${person.id}`;
        methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Scroll suave al formulario
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function resetForm() {
         form.reset();
         form.action = originalAction;
         methodField.innerHTML = '';
         submitButton.innerText = 'Registrar';
         cancelContainer.classList.add('hidden');
         // Limpiar calendario al resetear
         if (fp) fp.clear();
     }

     // Inicializar Flatpickr para Fecha de Nacimiento
      let fp = flatpickr("#fecha_nacimiento", {
          locale: "es",
          dateFormat: "Y-m-d",
          altInput: true,
          altFormat: "D, j M Y", // Formato amigable: sáb, 1 ene 2000
          maxDate: "today", // No permite fechas futuras
          disableMobile: "true", // Usar el diseño web en móviles
          animate: true,
          position: "auto center"
      });

      // Lógica de Búsqueda AJAX en tiempo real
      const searchInput = document.getElementById('searchInput');
      const personnelList = document.getElementById('personnelList');
      const paginationLinks = document.getElementById('paginationLinks');
      let timeout = null;

      searchInput.addEventListener('keyup', function() {
          clearTimeout(timeout);
          timeout = setTimeout(() => {
              const query = searchInput.value;
              
              // Ocultar paginación durante la búsqueda
              if (query.length > 0) {
                  paginationLinks.classList.add('hidden');
              } else {
                  paginationLinks.classList.remove('hidden');
              }

              fetch(`{{ route('serenazgo.index') }}?search=${query}`, {
                  headers: {
                      'X-Requested-With': 'XMLHttpRequest'
                  }
              })
              .then(response => response.text())
              .then(html => {
                  personnelList.innerHTML = html;
              })
              .catch(error => console.error('Error en la búsqueda:', error));
          }, 300); // Debounce de 300ms
      });

      function confirmDelete(id, name) {
          Swal.fire({
              title: '¿Estás seguro?',
              text: `Vas a eliminar permanentemente a "${name}". Esta acción no se puede deshacer.`,
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#ef4444',
              cancelButtonColor: '#3085d6',
              confirmButtonText: 'Sí, eliminar permanentemente',
              cancelButtonText: 'Cancelar'
          }).then((result) => {
              if (result.isConfirmed) {
                  document.getElementById(`delete-form-${id}`).submit();
              }
          });
      }

      function consultarDniSerenazgo() {
          const dniInput = document.getElementById('dni');
          const dni = dniInput.value.trim();
          
          if (dni.length !== 8) {
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

          fetch(`{{ route('api.consultar.dni') }}`, {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ dni: dni })
          })
              .then(res => res.json())
              .then(response => {
                  if (response.success) {
                      const data = response.data;
                      document.getElementById('nombres').value = data.nombres;
                      document.getElementById('apellido_paterno').value = data.apellido_paterno;
                      document.getElementById('apellido_materno').value = data.apellido_materno;
                      
                      // Nota: Decolecta no devuelve fecha de nacimiento, así que lo dejamos intacto
                      
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
                      Swal.fire('Atención', response.message || 'No se encontraron resultados para este DNI.', 'info');
                  }
              })
              .catch(err => {
                  console.error(err);
                  Swal.fire('Error', 'Hubo un problema al consultar el DNI.', 'error');
              });
      }
  </script>
  @endpush
