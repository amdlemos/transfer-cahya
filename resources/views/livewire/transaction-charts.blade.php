<div x-data="transactionCharts()" x-init="initCharts()">
    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Gráfico de tipos de transação (valores) --}}
        <x-card>
            <h3 class="text-lg font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Valores por Tipo de Transação</h3>
            <canvas id="transactionTypesChart" class="max-h-64"></canvas>
        </x-card>

        {{-- Gráfico de enviadas vs recebidas --}}
        <x-card>
            <h3 class="text-lg font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Enviadas vs Recebidas</h3>
            <canvas id="sentVsReceivedChart" class="max-h-64"></canvas>
        </x-card>

        {{-- Gráfico de contagem por tipo --}}
        <x-card>
            <h3 class="text-lg font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Quantidade por Tipo de Transação</h3>
            <canvas id="transactionTypesCountChart" class="max-h-64"></canvas>
        </x-card>

        {{-- Resumo estatístico --}}
        <x-card>
            <h3 class="text-lg font-semibold mb-4 text-zinc-900 dark:text-zinc-100">Resumo</h3>
            <div class="space-y-4">
                @php
                    $typesData = $this->getTransactionTypesData();
                    $sentReceivedData = $this->getSentVsReceivedData();
                    $countData = $this->getTransactionTypesCountData();
                    $sentAmount = $this->getTotalSent();
                    $receivedAmount = $this->getTotalReceived();
                    $depositedAmount = $this->getTotalDeposited();
                    $withdrawnAmount = $this->getTotalWithdrawn();
                    $balance = $receivedAmount + $depositedAmount - $sentAmount - $withdrawnAmount;
                @endphp
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Enviado:</span>
                    <span class="font-semibold text-red-600 dark:text-red-400">
                        R$ {{ number_format($sentAmount, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Recebido:</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">
                        R$ {{ number_format($receivedAmount, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Depositado:</span>
                    <span class="font-semibold text-blue-600 dark:text-blue-400">
                        R$ {{ number_format($depositedAmount, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Sacado:</span>
                    <span class="font-semibold text-orange-600 dark:text-orange-400">
                        R$ {{ number_format($withdrawnAmount, 2, ',', '.') }}
                    </span>
                </div>
                <hr class="border-zinc-200 dark:border-zinc-700" />
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Saldo:</span>
                    <span class="font-semibold {{ $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        R$ {{ number_format($balance, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total de Transações:</span>
                    <span class="font-semibold">
                        {{ array_sum($countData['data']) }}
                    </span>
                </div>
            </div>
        </x-card>
    </div>
</div>

<script>
    function transactionCharts() {
        return {
            initCharts() {
                // Aguarda um pouco para garantir que o DOM está pronto
                this.$nextTick(() => {
                    // Dados dos gráficos
                    const typesData = @json($this->getTransactionTypesData());
                    const sentReceivedData = @json($this->getSentVsReceivedData());
                    const countData = @json($this->getTransactionTypesCountData());

                    // Configuração comum do Chart.js
                    const chartOptions = {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                        },
                    };

                    // Gráfico de tipos de transação (valores) - Pizza
                    const typesCtx = document.getElementById('transactionTypesChart');
                    if (typesCtx && window.Chart) {
                        new Chart(typesCtx, {
                            type: 'pie',
                            data: {
                                labels: typesData.labels,
                                datasets: [{
                                    data: typesData.data,
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',  // Azul para Transferência
                                        'rgba(34, 197, 94, 0.8)',   // Verde para Depósito
                                        'rgba(239, 68, 68, 0.8)',   // Vermelho para Saque
                                    ],
                                    borderColor: [
                                        'rgba(59, 130, 246, 1)',
                                        'rgba(34, 197, 94, 1)',
                                        'rgba(239, 68, 68, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: chartOptions
                        });
                    }

                    // Gráfico de enviadas vs recebidas - Barra
                    const sentReceivedCtx = document.getElementById('sentVsReceivedChart');
                    if (sentReceivedCtx && window.Chart) {
                        new Chart(sentReceivedCtx, {
                            type: 'bar',
                            data: {
                                labels: sentReceivedData.labels,
                                datasets: [{
                                    label: 'Valor (R$)',
                                    data: sentReceivedData.data,
                                    backgroundColor: [
                                        'rgba(239, 68, 68, 0.8)',   // Vermelho para Enviadas
                                        'rgba(34, 197, 94, 0.8)',   // Verde para Recebidas
                                    ],
                                    borderColor: [
                                        'rgba(239, 68, 68, 1)',
                                        'rgba(34, 197, 94, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                ...chartOptions,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                });
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Gráfico de contagem por tipo - Doughnut
                    const countCtx = document.getElementById('transactionTypesCountChart');
                    if (countCtx && window.Chart) {
                        new Chart(countCtx, {
                            type: 'doughnut',
                            data: {
                                labels: countData.labels,
                                datasets: [{
                                    data: countData.data,
                                    backgroundColor: [
                                        'rgba(59, 130, 246, 0.8)',  // Azul para Transferência
                                        'rgba(34, 197, 94, 0.8)',   // Verde para Depósito
                                        'rgba(239, 68, 68, 0.8)',   // Vermelho para Saque
                                    ],
                                    borderColor: [
                                        'rgba(59, 130, 246, 1)',
                                        'rgba(34, 197, 94, 1)',
                                        'rgba(239, 68, 68, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: chartOptions
                        });
                    }
                });
            }
        }
    }
</script>

