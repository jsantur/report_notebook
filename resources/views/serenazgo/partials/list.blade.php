@forelse($personnel as $person)
<tr class="hover:bg-blue-50/50 transition-colors group {{ !$person->activo ? 'opacity-60 bg-gray-50' : '' }}">
    <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $person->dni }}</td>
    <td class="px-6 py-4 text-sm font-bold text-gray-700 uppercase">
        {{ $person->apellido_paterno }} {{ $person->apellido_materno }}, {{ $person->nombres }}
        @if(!$person->activo)
            <span class="ml-2 px-2 py-0.5 text-[8px] bg-red-100 text-red-600 rounded-full font-black uppercase">INACTIVO</span>
        @endif
    </td>
    <td class="px-6 py-4 text-sm text-gray-600">{{ $person->celular }}</td>
    <td class="px-6 py-4 text-sm text-gray-600">{{ $person->fecha_nacimiento }}</td>
    <td class="px-6 py-4">
        <span class="px-3 py-1 text-[10px] font-bold uppercase rounded-full {{ $person->perfil_trabajo === 'Chofer' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
            {{ $person->perfil_trabajo }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <div class="flex justify-center space-x-2">
            <button 
                onclick="editPerson({
                    id: {{ $person->id }},
                    nombres: '{{ $person->nombres }}',
                    apellido_paterno: '{{ $person->apellido_paterno }}',
                    apellido_materno: '{{ $person->apellido_materno }}',
                    dni: '{{ $person->dni }}',
                    fecha_nacimiento: '{{ $person->fecha_nacimiento }}',
                    celular: '{{ $person->celular }}',
                    perfil_trabajo: '{{ $person->perfil_trabajo }}',
                    nombre_foto: '{{ $person->nombre_foto }}'
                })"
                class="p-2 text-blue-500 hover:bg-blue-100 rounded-lg transition-colors"
                title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
            </button>
            
            <form action="{{ route('serenazgo.toggle-status', $person->id) }}" method="POST" class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" 
                    class="p-2 rounded-lg transition-colors {{ $person->activo ? 'text-orange-500 hover:bg-orange-100' : 'text-green-500 hover:bg-green-100' }}" 
                    title="{{ $person->activo ? 'Dar de Baja' : 'Dar de Alta' }}">
                    @if($person->activo)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </button>
            </form>

            <form id="delete-form-{{ $person->id }}" action="{{ route('serenazgo.destroy', $person->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="button" 
                    onclick="confirmDelete({{ $person->id }}, '{{ $person->apellido_paterno }} {{ $person->nombres }}')"
                    class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors" 
                    title="Eliminar Definitivamente">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
        No se encontró personal con esos criterios.
    </td>
</tr>
@endforelse
