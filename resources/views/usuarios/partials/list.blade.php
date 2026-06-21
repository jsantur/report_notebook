@forelse($users as $user)
<tr class="hover:bg-blue-50/30 transition-colors group">
    <td class="px-6 py-4">
        <div class="flex items-center">
            <div class="h-9 w-9 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 font-bold mr-3">
                {{ substr($user->username, 0, 1) }}
            </div>
            <span class="text-sm font-bold text-gray-700">{{ $user->username }}</span>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-gray-600 font-medium uppercase">{{ $user->name }}</td>
    <td class="px-6 py-4 text-center">
        <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
            {{ $user->role === 'admin' ? 'ADMIN' : 'USER' }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <span class="text-xs font-bold text-gray-500 italic">
            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $user->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $user->activo ? 'bg-green-500' : 'bg-red-500' }}"></span>
            {{ $user->activo ? 'ACTIVO' : 'SUSPENDIDO' }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <div class="flex justify-center space-x-1" x-data="{ userData: {{ json_encode($user) }} }">
            <button @click="editUser(userData)" class="p-2 text-blue-500 hover:bg-blue-100 rounded-lg transition-colors" title="Editar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </button>
            
            <button @click="toggleActive({{ $user->id }}, {{ $user->activo ? 'true' : 'false' }})" 
                class="p-2 rounded-lg transition-colors focus:outline-none" 
                title="{{ $user->activo ? 'Suspender' : 'Habilitar' }}">
                @if($user->activo)
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                @endif
            </button>

            <button @click="deleteUser({{ $user->id }})" class="p-2 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">No hay usuarios registrados en el sistema.</td>
</tr>
@endforelse
