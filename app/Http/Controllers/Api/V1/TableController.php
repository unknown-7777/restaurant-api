<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Table;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
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

        return $this->success(new TableResource($table));
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
}
