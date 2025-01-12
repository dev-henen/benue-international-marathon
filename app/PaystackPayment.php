<?php

namespace App;

use Yabacon\Paystack;

class PaystackPayment
{
    /**
     * Generate a transaction reference
     *
     * @param string $email Customer's email
     * @param int $amount Amount in kobo (e.g., 2000 NGN = 200000 kobo)
     * @return array Contains either 'success' and 'data' or 'success' and 'error'
     */
    public static function generateTransactionReference(string $email, int $amount): array
    {
        try {
            return [
                'success' => true,
                'data' => [
                    'email' => $email,
                    'amount' => $amount,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a Paystack transaction
     *
     * @param string $reference Transaction reference to verify
     * @return array Contains either 'success' and 'data' or 'success' and 'error'
     */
    public static function verifyTransaction(string $reference): array
    {
        if (empty($reference)) {
            return [
                'success' => false,
                'error' => 'Transaction reference is required for verification.',
            ];
        }

        $paystack = new Paystack($_ENV['PAYSTACK_PAYMENT_SECRET_KEY'] ?? '');

        try {
            $tranx = $paystack->transaction->verify(['reference' => $reference]);

            if ($tranx->data->status === 'success') {
                return [
                    'success' => true,
                    'data' => (array)$tranx->data,
                ];
            }

            return [
                'success' => false,
                'error' => 'Transaction verification failed: ' . $tranx->data->gateway_response,
            ];
        } catch (\Yabacon\Paystack\Exception\ApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
