<!-- Sección: Supervisores -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-10">
    <div class="relative border-b-2 border-blue-100 focus-within:border-blue-500 transition-colors">
        <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Supervisor de Campo</label>
        <div wire:ignore>
            <select id="supervisor_campo_select" name="supervisor_campo_id" x-model="supervisor_campo_id" class="w-full" required>
                <option value="" disabled selected>Selecciona</option>
            @foreach($supervisoresCampo as $sup)
                <option value="{{ $sup->id }}">{{ $sup->apellido_paterno }} {{ $sup->apellido_materno }} {{ $sup->nombres }}</option>
            @endforeach
            </select>
        </div>
    </div>
    <div class="relative border-b-2 border-blue-100 focus-within:border-blue-500 transition-colors">
        <label class="block text-xs font-bold text-blue-400 uppercase tracking-wider mb-1">Supervisor de C&aacute;maras</label>
        <div wire:ignore>
            <select id="supervisores_camaras_select" name="supervisores_camaras[]" x-model="supervisores_camaras" multiple class="w-full" required>
            @foreach($supervisoresCamaras as $sup)
                <option value="{{ $sup->id }}">{{ $sup->apellido_paterno }} {{ $sup->apellido_materno }} {{ $sup->nombres }}</option>
            @endforeach
            </select>
        </div>
    </div>
</div>
