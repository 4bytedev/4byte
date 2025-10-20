<x-filament-panels::page>
    <x-filament-panels::form id="form" wire:submit="save">
        {{ $this->form }}
        <x-filament-panels::form.actions
            :actions="$this->formActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>