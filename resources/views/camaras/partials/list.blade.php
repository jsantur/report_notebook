@forelse($camaras as $camara)
    <tr class="hover:bg-blue-50/50 transition-colors">
        <td class="px-6 py-4">
            <span class="text-sm font-bold text-gray-800 uppercase">{{ $camara->nombre }}</span>
        </td>
        <td class="px-6 py-4">
            <span class="text-sm text-gray-600">{{ $camara->ubicacion ?? '-' }}</span>
        </td>
        <td class="px-6 py-4 text-center">
            @if($camara->activa)
                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-green-100 text-green-800 uppercase">
                    Registrada
                </span>
            @else
                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full bg-red-100 text-red-800 uppercase">
                    Dada de baja
                </span>
            @endif
            <p class="text-[9px] text-gray-400 mt-1 uppercase">Cambiar estado oficial</p>
            <form action="{{ route('camaras.toggle-status', $camara->id) }}" method="POST" class="inline-block mt-1">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-xs font-bold {{ $camara->activa ? 'text-red-500 hover:text-red-700' : 'text-green-500 hover:text-green-700' }} underline">
                    {{ $camara->activa ? 'Dar de baja' : 'Reactivar' }}
                </button>
            </form>
        </td>
        <td class="px-6 py-4 text-center">
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="editCamara({{ json_encode($camara) }})" class="text-blue-600 hover:text-blue-800 p-1 hover:bg-blue-50 rounded transition-colors" title="Editar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </button>
                <button type="button" onclick="confirmDelete({{ $camara->id }})" class="text-red-500 hover:text-red-700 p-1 hover:bg-red-50 rounded transition-colors" title="Eliminar Permanentemente">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
                <form id="delete-form-{{ $camara->id }}" action="{{ route('camaras.destroy', $camara->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron cámaras registradas</td>
    </tr>
@endforelse
