<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço responsável por autorizar transações através de um serviço externo
 */
class AuthorizationService
{
    private const AUTHORIZATION_URL = 'https://util.devi.tools/api/v2/authorize';

    private const SUCCESS_PROBABILITY = 0.65;

    /**
     * Consulta o serviço de autorização externo
     *
     * @param  Transaction  $transaction  Transação a ser autorizada
     * @return bool True se autorizado, False caso contrário
     */
    public function authorize(Transaction $transaction): bool
    {
        try {
            $response = Http::timeout(5)
                ->withoutVerifying()  // Desabilita verificação SSL
                ->get(self::AUTHORIZATION_URL);

            // Se a API retornar um status que não é 200, usar mock
            if ($response->status() !== 200) {
                return $this->mockAuthorize($transaction);
            }

            $data = $response->json();
            // A API retorna: ['status' => 'success|fail']
            $authorized = isset($data['status']) && $data['status'] === 'success';

            // Registrar autorização
            $transaction->authorization()->create([
                'authorized' => $authorized,
                'response' => $response->body(),
                'attempted_at' => now(),
            ]);

            return $authorized;
        } catch (\Exception $e) {
            Log::warning('Authorization service error, using mock', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);

            // Se a API externo falhar, usar mock
            return $this->mockAuthorize($transaction);
        }
    }

    /**
     * Simula uma resposta de autorização com probabilidade de sucesso
     * 65% de sucesso e 35% de falha
     *
     * @param  Transaction  $transaction  Transação a ser autorizada
     * @return bool True se autorizado, False caso contrário
     */
    private function mockAuthorize(Transaction $transaction): bool
    {
        // Gera um número aleatório entre 0 e 1
        $randomValue = (float) rand(0, 100) / 100;

        // Autoriza se o valor aleatório for menor que 0.65 (65% de sucesso)
        $authorized = $randomValue < self::SUCCESS_PROBABILITY;

        // Registrar autorização mock
        $transaction->authorization()->create([
            'authorized' => $authorized,
            'response' => json_encode([
                'status' => $authorized ? 'success' : 'fail',
                'message' => 'Autorização simulada (API indisponível)',
                'mock' => true,
            ]),
            'attempted_at' => now(),
        ]);

        Log::info('Mock authorization used', [
            'transaction_id' => $transaction->id,
            'authorized' => $authorized,
        ]);

        return $authorized;
    }
}
