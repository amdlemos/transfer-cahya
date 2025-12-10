<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4">Realizar Transferência</h2>

        {{-- Saldo Atual --}}
        <div class="mb-6 p-4 bg-gray-100 rounded-lg">
            <p class="text-sm text-gray-600">Saldo Atual</p>
            <p class="text-3xl font-bold text-green-600" x-data="{ balance: {{ auth()->user()->wallet->balance }} }"
                @balance-updated.window="balance = {{ auth()->user()->wallet->fresh()->balance }}">
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

        {{-- Formulário de Transferência --}}
        <form wire:submit.prevent="transfer" class="space-y-4">
            <div>
                <label for="payeeId" class="block text-sm font-medium text-gray-700 mb-2">
                    Destinatário
                </label>
                <select id="payeeId" wire:model="payeeId"
                    class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">-- selecionar usuário --</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                @error('payeeId')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                    Valor da Transferência
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        R$
                    </span>
                    <input type="number" id="amount" wire:model="amount" step="0.01" min="0.01"
                        placeholder="0,00"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="transfer"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="transfer">Transferir</span>
                <span wire:loading wire:target="transfer">Processando...</span>
            </button>
        </form>
    </div>

    {{-- Histórico Recente --}}
    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold mb-4">Histórico de Transferências (Enviadas)</h3>
        <div class="space-y-2">
            @forelse(auth()->user()->sentTransactions()->where('description', 'Transferência entre usuários')->latest()->take(5)->get() as $transaction)
                <div class="flex justify-between items-center py-2 border-b">
                    <div>
                        <p class="font-medium">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-xs text-gray-500">Para: {{ $transaction->payee->name ?? '—' }}</p>
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
                <p class="text-gray-500 text-center py-4">Nenhuma transferência realizada ainda.</p>
            @endforelse
        </div>
    </div>
</div>
