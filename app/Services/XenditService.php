<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class XenditService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.xendit.secret_key');
        $this->baseUrl = 'https://api.xendit.co';
    }

    /**
     * Create invoice for payment
     */
    public function createInvoice($order)
    {
        try {
            // Format phone number from shipping info
            $phone = $order->shipping_phone ?? '';
            if (!empty($phone) && !str_starts_with($phone, '+')) {
                // Remove leading zero and add +62
                $phone = '+62' . ltrim($phone, '0');
            }

            $payload = [
                'external_id' => $order->order_number,
                'amount' => (float) $order->total_amount,
                'description' => 'Pembayaran Order #' . $order->order_number,
                'invoice_duration' => 86400, // 24 hours
                'currency' => 'IDR',
                'success_redirect_url' => route('payment.success', $order->id),
                'failure_redirect_url' => route('payment.failed', $order->id),
            ];

            // Add customer info
            $payload['customer'] = [
                'given_names' => $order->shipping_name ?? $order->user->name,
                'email' => $order->shipping_email ?? $order->user->email,
            ];
            
            if (!empty($phone)) {
                $payload['customer']['mobile_number'] = $phone;
            }

            // Add notification preference
            $payload['customer_notification_preference'] = [
                'invoice_created' => ['email'],
                'invoice_paid' => ['email'],
            ];

            \Log::info('Xendit Create Invoice Request', $payload);

            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($this->baseUrl . '/v2/invoices', $payload);

            if (!$response->successful()) {
                \Log::error('Xendit API Error Response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                
                return [
                    'success' => false,
                    'message' => $response->json()['message'] ?? 'Failed to create invoice',
                ];
            }

            $invoice = $response->json();
            
            \Log::info('Xendit Invoice Created', ['invoice_id' => $invoice['id']]);
            
            return [
                'success' => true,
                'invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url'],
                'expiry_date' => $invoice['expiry_date'],
            ];
        } catch (Exception $e) {
            \Log::error('Xendit Invoice Exception', [
                'message' => $e->getMessage(),
                'order_id' => $order->id ?? 'N/A',
                'order_number' => $order->order_number ?? 'N/A',
            ]);
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get invoice details
     */
    public function getInvoice($invoiceId)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/v2/invoices/' . $invoiceId);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'Failed to get invoice',
                ];
            }

            return [
                'success' => true,
                'data' => $response->json(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create Virtual Account
     */
    public function createVirtualAccount($order, $bankCode = 'BCA')
    {
        // Note: For SDK v7, VA might need different implementation
        // Keeping this for reference, but Invoice API is recommended
        return [
            'success' => false,
            'message' => 'Please use Invoice API which supports multiple payment methods including VA',
        ];
    }

    /**
     * Build order items for invoice
     */
    private function buildOrderItems($order)
    {
        $items = [];
        
        foreach ($order->orderItems as $item) {
            $items[] = [
                'name' => $item->product_name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'category' => $item->product->category->name ?? 'Produk',
            ];
        }

        // Add shipping cost
        if ($order->shipping_amount > 0) {
            $items[] = [
                'name' => 'Biaya Pengiriman',
                'quantity' => 1,
                'price' => $order->shipping_amount,
                'category' => 'Pengiriman',
            ];
        }

        return $items;
    }

    /**
     * Verify callback signature
     */
    public function verifyCallback($callbackToken)
    {
        return $callbackToken === config('services.xendit.callback_token');
    }
}
