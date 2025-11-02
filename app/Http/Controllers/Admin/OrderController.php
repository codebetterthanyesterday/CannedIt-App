<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user', 'orderItems.product');
        
        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Search by order number or customer name
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $orders = $query->latest()->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'orderItems.product');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed,refunded',
            'tracking_number' => 'nullable|string|max:100',
            'shipping_courier' => 'nullable|string|max:50',
            'shipping_service' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $order->update($validated);

        // If order is shipped, send notification (implement later)
        if ($validated['status'] === 'shipped' && isset($validated['tracking_number'])) {
            // Send shipping notification
        }

        return redirect()->back()
            ->with('success', 'Status pesanan berhasil diperbarui!');
    }
}
