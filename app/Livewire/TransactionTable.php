<?php

namespace App\Livewire;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

/**
 * Componente PowerGrid para exibição e gerenciamento de tabela de transações
 */
final class TransactionTable extends PowerGridComponent
{
    public string $tableName = 'transactionTable';

    /**
     * Configura as opções da tabela PowerGrid
     */
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /**
     * Define a fonte de dados para a tabela
     */
    public function datasource(): Builder
    {
        $userId = Auth::id();

        return Transaction::query()
            ->where(function (Builder $query) use ($userId) {
                $query->where('payer_id', $userId)
                    ->orWhere('payee_id', $userId);
            })
            ->with(['payer:id,name', 'payee:id,name'])
            ->orderByDesc('created_at');
    }

    /**
     * Define os relacionamentos para busca
     */
    public function relationSearch(): array
    {
        return [
            'payer' => ['name'],
            'payee' => ['name'],
        ];
    }

    /**
     * Define os campos a serem exibidos na tabela
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('payer_name', fn ($transaction) => $transaction->payer->name ?? '—')
            ->add('payee_name', fn ($transaction) => $transaction->payee->name ?? '—')
            ->add('amount')
            /* ->add('status', fn ($transaction) => $transaction->status->label()) */
            ->add('description')
            ->add('created_at');
    }

    /**
     * Define as colunas a serem exibidas na tabela
     */
    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable()
                ->searchable(),

            Column::make('Pagador', 'payer_name')
                ->searchable(),

            Column::make('Beneficiário', 'payee_name')
                ->searchable(),
            Column::make('Valor', 'amount')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status', 'status.status')
                ->sortable()
                ->searchable(),

            Column::make('Tipo', 'type', 'type.type')
                ->sortable()
                ->searchable(),

            Column::make('Descrição', 'description')
                ->sortable()
                ->searchable(),

            Column::make('Data', 'created_at_formatted', 'created_at')
                ->sortable(),
        ];
    }

    /**
     * Define os filtros disponíveis na tabela
     */
    public function filters(): array
    {
        return [
            // Filtro de Enum para Status
            Filter::enumSelect('status', 'status')
                ->datasource(TransactionStatus::cases())
                ->optionValue('value')
                ->optionLabel('status.status'),

            Filter::enumSelect('type', 'type')
                ->datasource(TransactionType::cases())
                ->optionValue('value')
                ->optionLabel('type.type'),
        ];
    }
}
