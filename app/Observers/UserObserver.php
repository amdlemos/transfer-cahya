<?php

namespace App\Observers;

use App\Models\User;

/**
 * Observer para criar Wallet automaticamente para usuário
 */
class UserObserver
{
    public function created(User $user): void
    {
        $user->wallet()->create([
            'balance' => 0,
        ]);
    }
}
