<x-filament-panels::page>
  <form wire:submit="generate">
    {{ $this->form }}
    <div class="w-full flex items-center justify-end gap-4 mt-6">
      <x-filament::button type="submit" class="flex items-center">Generar Reporte</x-filament::button>
      <x-filament::button type="button" color="danger" wire:click="resetForm">Limpiar</x-filament::button>
    </div>
  </form>

  @if($showTable)
    {{ $this->table}}
  @endif
</x-filament-panels::page>
