<x-layouts.app>
    <div>
        {{-- Gráficos de Transações --}}
        <livewire:transaction-charts />

        {{-- Tabela PowerGrid --}}
        <x-card class="mt-6">
            <livewire:transaction-table />
        </x-card>
    </div>
</x-layouts.app>

