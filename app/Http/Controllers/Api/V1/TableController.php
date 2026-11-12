<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $tables = Table::orderBy('number')->get();

        return $this->success(TableResource::collection($tables));
    }

    public function showByQrCode(string $qrCode): JsonResponse
    {
        $table = Table::where('qr_code', $qrCode)->firstOrFail();
        $this->loadUnpaidBillOrders($table);

        return $this->success(new TableResource($table));
    }

    public function requestBillFromQr(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_qr_code' => ['required', 'uuid', 'exists:tables,qr_code'],
            'method' => ['required', 'in:cash,card,qris'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $table = Table::where('qr_code', $data['table_qr_code'])->lockForUpdate()->firstOrFail();
            $unpaidOrders = $table->orders()
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                ->whereDoesntHave('payment')
                ->lockForUpdate()
                ->get();

            if ($unpaidOrders->isEmpty()) {
                return ['error' => 'There are no unpaid orders for this table.'];
            }

            if ($unpaidOrders->contains(fn ($order) => $order->status !== 'served')) {
                return ['error' => 'Please wait until all table orders have been served before requesting one bill.'];
            }

            if (!$table->bill_requested_at) {
                $table->update([
                    'bill_requested_at' => now(),
                    'bill_requested_method' => $data['method'],
                ]);
            }

            return ['table' => $table->fresh()];
        });

        if (isset($result['error'])) {
            return $this->error($result['error'], 422);
        }

        $this->loadUnpaidBillOrders($result['table']);

        return $this->success(new TableResource($result['table']), 'One bill requested for all served table orders.');
    }

    public function store(StoreTableRequest $request): JsonResponse
    {
        $table = Table::create([
            'number'   => $request->number,
            'capacity' => $request->capacity ?? 4,
            'status'   => 'available',
            'qr_code'  => Str::uuid(),
        ]);

        return $this->success(new TableResource($table), 'Table created.', 201);
    }

    public function show(Table $table): JsonResponse
    {
        return $this->success(new TableResource($table));
    }

    public function update(UpdateTableRequest $request, Table $table): JsonResponse
    {
        if ($request->filled('status') && $request->status !== 'occupied') {
            $hasOpenOrders = $table->orders()
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                ->exists();

            if ($hasOpenOrders) {
                return $this->error('This table has an open order and must remain occupied.', 422);
            }
        }

        $table->update($request->only(['status', 'capacity']));

        return $this->success(new TableResource($table), 'Table updated.');
    }

    public function destroy(Table $table): JsonResponse
    {
        $table->delete();

        return $this->success(null, 'Table deleted.');
    }

    private function loadUnpaidBillOrders(Table $table): void
    {
        $table->load(['orders' => fn ($query) => $query
            ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
            ->whereDoesntHave('payment')]);
    }
}
