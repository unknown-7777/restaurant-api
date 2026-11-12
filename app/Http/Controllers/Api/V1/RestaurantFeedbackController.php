<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RestaurantFeedback;
use App\Models\Table;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantFeedbackController extends Controller
{
    use ApiResponse;

    public function storeFromQr(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'table_qr_code' => ['required', 'uuid', 'exists:tables,qr_code'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $table = Table::where('qr_code', $data['table_qr_code'])->firstOrFail();
        abort_unless($order->table_id === $table->id, 404, 'Order not found for this table.');
        abort_unless($order->status === 'paid' && $order->payment()->exists(), 422, 'Feedback is available after payment.');

        $feedback = RestaurantFeedback::updateOrCreate(
            ['order_id' => $order->id],
            [
                'table_id' => $table->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]
        );

        return $this->success($feedback, 'Thank you for your feedback.');
    }

    public function index(): JsonResponse
    {
        $feedback = RestaurantFeedback::with(['table:id,number', 'order:id,total_amount'])
            ->latest()
            ->paginate(25);

        return $this->success([
            'feedback' => $feedback->items(),
            'meta' => [
                'current_page' => $feedback->currentPage(),
                'last_page' => $feedback->lastPage(),
                'total' => $feedback->total(),
            ],
        ]);
    }
}
