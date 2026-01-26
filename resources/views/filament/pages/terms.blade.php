<x-filament-panels::page>
    <form wire:submit.prevent="submit">

        {{ $this->form }}

        <x-filament::button wire:click="submit" wire:loading.attr="disabled" class="my-4">
            <span wire:loading.remove wire:target="submit">
                Save
            </span>
            <span wire:loading class="flex items-center gap-1">
                Saving
            </span>
        </x-filament::button>
    </form>

</x-filament-panels::page>
