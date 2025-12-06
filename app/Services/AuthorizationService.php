<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthorizationService
{
    private const AUTHORIZATION_URL = 'https://util.devi.tools/api/v2/authorize';

    public function authorize(Transaction $transaction): bool
    {
        try {
            $response = Http::timeout(5)->get(self::AUTHORIZATION_URL);

            $data = $response->json();

            // Registrar autorização
            $transaction->authorization()->create([
                'authorized' => $data['data']['authorization'] ?? false,
                'response' => $response->body(),
                'attempted_at' => now(),
            ]);

            return $data['data']['authorization'] ?? false;
        } catch (\Exception $e) {
            Log::error('Authorization service error', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
