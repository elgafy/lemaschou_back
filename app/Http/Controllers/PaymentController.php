<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payments\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    /**
     * Initiate a payment for an order.
     * POST /api/payments/initiate
     */
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
        ]);

        $order = Order::with('reservation')->findOrFail($validated['order_id']);

        if (! $order->reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Order has no associated reservation',
            ], 422);
        }

        if ($order->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Order is already paid',
            ], 422);
        }

        try {
            $result = $this->paymentService->initiate($order, $order->reservation);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment_id' => $result['payment']->id,
                    'redirect_url' => $result['redirect_url'],
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Handle incoming webhook from payment gateway.
     * POST /api/payments/webhook
     */
    public function webhook(Request $request)
    {
        try {
            $this->paymentService->handleWebhook($request->all());
        } catch (\Throwable $e) {
            Log::error('Payment webhook error', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);
        }

        // Always return 200 to acknowledge receipt
        return response('', 200);
    }

    /**
     * Customer redirect after successful payment.
     * GET /api/payments/success?order_id=X
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::with('reservation')->find($orderId);

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $orderId,
                'order_status' => $order?->status ?? 'unknown',
                'message' => 'Payment processed. Thank you!',
            ],
        ]);
    }

    /**
     * Customer redirect after failed payment.
     * GET /api/payments/failure?order_id=X
     */
    public function failure(Request $request)
    {
        $orderId = $request->query('order_id');
        $order = Order::find($orderId);

        return response()->json([
            'success' => false,
            'data' => [
                'order_id' => $orderId,
                'order_status' => $order?->status ?? 'unknown',
                'message' => 'Payment failed. Please try again.',
            ],
        ]);
    }
}
