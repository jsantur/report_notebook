@foreach($vehiculos as $v)
<tr class="hover:bg-gray-50 transition-colors">
    <td class="px-6 py-4 border-b border-gray-100">
        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-bold">{{ $v->placa }}</span>
    </td>
    <td class="px-6 py-4 border-b border-gray-100">
        <div class="flex items-center">
            <span class="text-sm font-semibold text-gray-700 uppercase">{{ $v->tipo }}</span>
        </div>
    </td>
    <td class="px-6 py-4 border-b border-gray-100">
        <span class="text-sm font-medium text-gray-600 uppercase">{{ $v->nro_unidad }}</span>
    </td>
    <td class="px-6 py-4 border-b border-gray-100">
        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $v->tipo_patrullaje == 'Vehicular' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-orange-50 text-orange-600 border border-orange-100' }}">
            {{ $v->tipo_patrullaje }}
        </span>
    </td>
    <td class="px-6 py-4 border-b border-gray-100 text-center">
        <span class="px-2 py-1 rounded-full text-[9px] font-bold uppercase {{ $v->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            {{ $v->activo ? 'Operativo' : 'Suspendido' }}
        </span>
    </td>
    <td class="px-6 py-4 border-b border-gray-100">
        <div class="flex justify-center space-x-3">
            <form action="{{ route('vehiculos.toggle-status', $v->id) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" class="{{ $v->activo ? 'text-green-500 hover:text-green-700' : 'text-gray-400 hover:text-gray-600' }} transition-colors" title="{{ $v->activo ? 'Suspender' : 'Activar' }}">
                    @if($v->activo)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    @endif
                </button>
            </form>
            <button onclick='editVehiculo(@json($v))' class="text-blue-500 hover:text-blue-700 transition-colors" title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </button>
            <form id="delete-form-{{ $v->id }}" action="{{ route('vehiculos.destroy', $v->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmDelete({{ $v->id }})" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@endforeach

@if($vehiculos->isEmpty())
<tr>
    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron vehículos registrados</td>
</tr>
@endif
