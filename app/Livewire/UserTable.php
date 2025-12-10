<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

/**
 * Componente PowerGrid para exibição e gerenciamento de tabela de usuários
 */
final class UserTable extends PowerGridComponent
{
    public string $tableName = 'userTable';

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
        // Join com a tabela wallets e expor o saldo como alias `balance`
        // para que PowerGrid possa ordenar por essa coluna diretamente.
        return User::query()
            ->leftJoin('wallets', 'wallets.user_id', '=', 'users.id')
            ->select('users.*', 'wallets.balance as balance');
    }

    /**
     * Define os relacionamentos para busca
     */
    public function relationSearch(): array
    {
        return [];
    }

    /**
     * Define os campos a serem exibidos na tabela
     */
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('full_name')
            ->add('document')
            ->add('balance', fn ($user) => $user->balance ?? 0)
            ->add('type_label', fn ($user) => $user->type->labels())
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

            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Full name', 'full_name')
                ->sortable()
                ->searchable(),

            Column::make('Document', 'document')
                ->sortable()
                ->searchable(),

            Column::add()
                ->title('Balance')
                ->field('balance')
                ->sortable(),

            Column::make('Type', 'type_label')
                ->searchable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),
        ];
    }

    /**
     * Define os filtros disponíveis na tabela
     */
    public function filters(): array
    {
        return [
        ];
    }
}
