<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Table;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $startOfDay = now()->startOfDay();
        $endOfDay = $startOfDay->copy()->endOfDay();
        $revenueToday = Payment::query()
            ->whereBetween('processed_at', [$startOfDay, $endOfDay])
            ->selectRaw('COALESCE(SUM(amount - change_amount), 0) as total')
            ->value('total');

        $recentOrders = Order::with('table')
            ->latest()
            ->limit(10)
            ->get();

        return $this->success([
            'stats' => [
                'orders_today' => Order::whereBetween('created_at', [$startOfDay, $endOfDay])->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'occupied_tables' => Table::where('status', 'occupied')->count(),
                'revenue_today' => number_format((float) $revenueToday, 2, '.', ''),
            ],
            'recent_orders' => OrderResource::collection($recentOrders),
        ]);
    }
}
