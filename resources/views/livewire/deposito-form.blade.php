<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">Realizar Depósito</h2>

        {{-- Saldo Atual --}}
        <div class="mb-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-sm text-gray-600">Saldo Atual</p>
            <p class="text-3xl font-bold text-green-600" x-data="{ balance: @entangle('currentBalance') }">
                R$ <span x-text="balance.toFixed(2).replace('.', ',')"></span>
            </p>
        </div>

        {{-- Mensagens --}}
        @if ($successMessage)
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ $successMessage }}
            </div>
        @endif

        @if ($errorMessage)
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ $errorMessage }}
            </div>
        @endif

        {{-- Formulário de Depósito --}}
        <form wire:submit.prevent="deposit" class="space-y-4">
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Valor do Depósito
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        R$
                    </span>
                    <input type="number" id="amount" wire:model="amount" step="0.01" min="0.01"
                        placeholder="0,00"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        {{ $failedTransaction ? 'disabled' : '' }}>
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if (!$failedTransaction)
                <button type="submit" wire:loading.attr="disabled" wire:target="deposit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="deposit">Depositar</span>
                    <span wire:loading wire:target="deposit">Processando...</span>
                </button>
            @endif
        </form>

        {{-- Botão Tentar Novamente --}}
        @if ($failedTransaction)
            <div class="mt-4 space-y-3">
                <button wire:click="retry" wire:loading.attr="disabled" wire:target="retry"
                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 disabled:opacity-50">
                    <span wire:loading.remove wire:target="retry">Tentar Novamente</span>
                    <span wire:loading wire:target="retry">Processando...</span>
                </button>

                <button wire:click="$set('failedTransaction', null)"
                    class="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Novo Depósito
                </button>

                <div class="text-sm text-gray-600 text-center">
                    Transação #{{ $failedTransaction->id }} -
                    R$ {{ number_format($failedTransaction->amount, 2, ',', '.') }}
                </div>
            </div>
        @endif
    </div>

    {{-- Histórico Recente --}}
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Histórico de Depósitos</h3>
        <div class="space-y-2">
            @forelse(auth()->user()->sentTransactions()->where('description', 'Depósito')->latest()->take(5)->get() as $transaction)
                <div class="flex justify-between items-center py-2 border-b">
                    <div>
                        <p class="font-medium">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $transaction->status->value === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $transaction->status->value === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $transaction->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    ">
                        {{ $transaction->status->label() }}
                    </span>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">Nenhum depósito realizado ainda.</p>
            @endforelse
        </div>
    </div>
</div>
