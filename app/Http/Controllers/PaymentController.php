<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $xenditService;

    public function __construct(XenditService $xenditService)
    {
        $this->xenditService = $xenditService;
    }

    /**
     * Create payment for order
     */
    public function createPayment(Request $request, Order $order)
    {
        // Check if order belongs to authenticated user
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('orders.index')
                ->with('error', 'Anda tidak memiliki akses ke pesanan ini');
        }

        // Check if order is pending
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $order->id)
                ->with('info', 'Pesanan sudah dibayar atau dibatalkan');
        }

        // Create Xendit invoice
        $result = $this->xenditService->createInvoice($order);

        if (!$result['success']) {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Gagal membuat invoice pembayaran: ' . $result['message']);
        }

        // Update order with Xendit data
        $order->update([
            'xendit_invoice_id' => $result['invoice_id'],
            'xendit_invoice_url' => $result['invoice_url'],
            'xendit_expired_at' => $result['expiry_date'],
        ]);

        // Redirect to Xendit payment page
        return redirect($result['invoice_url']);
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess(Order $order)
    {
        // Reload order with relationships first
        $order->load('orderItems.product.category', 'user');
        
        // Check payment status from Xendit only if status is still pending
        if ($order->payment_status === 'pending' && $order->xendit_invoice_id) {
            $result = $this->xenditService->getInvoice($order->xendit_invoice_id);
            
            if ($result['success']) {
                $invoice = $result['data'];
                
                \Log::info('Xendit Invoice Status Check', [
                    'order_id' => $order->id,
                    'invoice_status' => $invoice['status'] ?? 'N/A',
                    'invoice_status_type' => gettype($invoice['status'] ?? null),
                    'current_payment_status' => $order->payment_status,
                ]);
                
                // Update order if paid
                if (isset($invoice['status']) && strtoupper((string)$invoice['status']) === 'PAID') {
                    try {
                        // Use DB::table to bypass Eloquent completely
                        DB::table('orders')
                            ->where('id', $order->id)
                            ->update([
                                'payment_status' => 'paid',
                                'status' => 'processing',
                                'paid_at' => now(),
                                'xendit_paid_at' => isset($invoice['paid_at']) ? $invoice['paid_at'] : now(),
                                'payment_channel' => isset($invoice['payment_method']) ? $invoice['payment_method'] : null,
                                'updated_at' => now(),
                            ]);
                        
                        \Log::info('Order status updated successfully via DB::table', ['order_id' => $order->id]);
                        
                        // Reload order to get fresh data
                        $order = Order::with('orderItems.product.category', 'user')->find($order->id);
                    } catch (\Exception $e) {
                        \Log::error('Failed to update order status', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                }
            }
        }
        
        return view('orders.payment-success', compact('order'));
    }

    /**
     * Payment failed callback
     */
    public function paymentFailed(Order $order)
    {
        // Reload order with relationships
        $order->load('orderItems.product.category', 'user');
        
        return view('orders.payment-failed', compact('order'));
    }

    /**
     * Xendit webhook callback
     */
    public function webhook(Request $request)
    {
        // Verify callback token
        $callbackToken = $request->header('x-callback-token');
        
        if (!$this->xenditService->verifyCallback($callbackToken)) {
            Log::warning('Invalid Xendit callback token');
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Get webhook data
        $data = $request->all();
        Log::info('Xendit Webhook', $data);

        // Find order by external_id (order_number)
        $order = Order::where('order_number', $data['external_id'])->first();

        if (!$order) {
            Log::error('Order not found: ' . $data['external_id']);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Handle payment status
        if ($data['status'] === 'PAID') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'paid_at' => now(),
                'xendit_paid_at' => $data['paid_at'] ?? now(),
                'payment_channel' => $data['payment_channel'] ?? null,
                'payment_reference' => $data['payment_id'] ?? null,
            ]);

            Log::info('Order paid: ' . $order->order_number);
        } elseif ($data['status'] === 'EXPIRED') {
            $order->update([
                'payment_status' => 'expired',
                'status' => 'cancelled',
            ]);

            Log::info('Order expired: ' . $order->order_number);
        }

        return response()->json(['message' => 'Webhook processed']);
    }

    /**
     * Check payment status
     */
    public function checkStatus(Order $order)
    {
        if (!$order->xendit_invoice_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan'
            ]);
        }

        $result = $this->xenditService->getInvoice($order->xendit_invoice_id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ]);
        }

        $invoice = $result['data'];

        return response()->json([
            'success' => true,
            'status' => $invoice['status'],
            'payment_method' => $invoice['payment_method'] ?? null,
            'paid_at' => $invoice['paid_at'] ?? null,
        ]);
    }
}
