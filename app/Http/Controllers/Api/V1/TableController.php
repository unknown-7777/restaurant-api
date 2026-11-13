<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Table\StoreTableRequest;
use App\Http\Requests\Table\UpdateTableRequest;
use App\Http\Resources\TableResource;
use App\Models\Order;
use App\Models\Table;
use App\Models\QrGuestSession;
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

        return $this->success(new TableResource($table));
    }

    public function startGuestSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_qr_code' => ['required', 'uuid', 'exists:tables,qr_code'],
            'guest_session_token' => ['sometimes', 'string', 'size:64'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $table = Table::where('qr_code', $data['table_qr_code'])->lockForUpdate()->firstOrFail();
            $now = now();

            if (isset($data['guest_session_token'])) {
                $session = QrGuestSession::where('table_id', $table->id)
                    ->where('token_hash', hash('sha256', $data['guest_session_token']))
                    ->whereNull('closed_at')
                    ->where('expires_at', '>', $now)
                    ->lockForUpdate()
                    ->first();

                if (!$session) {
                    return ['error' => 'This ordering visit has ended. Scan the table QR code to start a new visit.', 'status' => 410];
                }

                $session->update(['expires_at' => $now->copy()->addHours(4)]);

                return [
                    'token' => $data['guest_session_token'],
                    'expires_at' => $session->expires_at->toDateTimeString(),
                    'status' => 200,
                ];
            }

            if ($table->status === 'reserved') {
                return ['error' => 'This table is reserved. Please ask a staff member for help.', 'status' => 409];
            }

            $activeSession = QrGuestSession::where('table_id', $table->id)
                ->whereNull('closed_at')
                ->where('expires_at', '>', $now)
                ->lockForUpdate()
                ->first();

            if ($activeSession) {
                return ['error' => 'This table already has an active ordering visit. Please ask a staff member for help.', 'status' => 409];
            }

            if (!$table->qr_access_enabled) {
                return ['error' => 'QR ordering is closed for this table. Please ask a staff member to prepare it for the next guest.', 'status' => 409];
            }

            $token = Str::random(64);
            $session = QrGuestSession::create([
                'table_id' => $table->id,
                'token_hash' => hash('sha256', $token),
                'expires_at' => $now->copy()->addHours(4),
            ]);
            $table->update([
                'status' => 'occupied',
                'qr_access_enabled' => false,
            ]);

            return [
                'token' => $token,
                'expires_at' => $session->expires_at->toDateTimeString(),
                'status' => 201,
            ];
        });

        if (isset($result['error'])) {
            return $this->error($result['error'], $result['status']);
        }

        $status = $result['status'];
        unset($result['status']);

        return $this->success($result, $status === 201 ? 'Guest ordering session started.' : 'Guest ordering session resumed.', $status);
    }

    public function finishGuestSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_qr_code' => ['required', 'uuid', 'exists:tables,qr_code'],
            'guest_session_token' => ['required', 'string', 'size:64'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $table = Table::where('qr_code', $data['table_qr_code'])->lockForUpdate()->firstOrFail();
            $session = QrGuestSession::where('table_id', $table->id)
                ->where('token_hash', hash('sha256', $data['guest_session_token']))
                ->whereNull('closed_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return ['error' => 'This ordering visit has already ended.', 'status' => 410];
            }

            $hasOpenSessionOrders = Order::where('qr_guest_session_id', $session->id)
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                ->exists();

            if ($hasOpenSessionOrders) {
                return ['error' => 'Please ask a staff member before leaving; this visit still has unpaid or unfinished orders.'];
            }

            $session->update(['closed_at' => now()]);

            $hasOpenTableOrders = Order::where('table_id', $table->id)
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                ->exists();

            $hasAnotherActiveSession = QrGuestSession::where('table_id', $table->id)
                ->whereNull('closed_at')
                ->where('expires_at', '>', now())
                ->exists();

            $table->update([
                'status' => $hasOpenTableOrders || $hasAnotherActiveSession ? 'occupied' : 'available',
                'qr_access_enabled' => false,
            ]);

            return ['table' => $table->fresh()];
        });

        if (isset($result['error'])) {
            return $this->error($result['error'], $result['status'] ?? 422);
        }

        return $this->success(new TableResource($result['table']), 'Guest visit finished and table status updated.');
    }

    public function requestBillFromQr(Request $request): JsonResponse
    {
        $data = $request->validate([
            'table_qr_code' => ['required', 'uuid', 'exists:tables,qr_code'],
            'guest_session_token' => ['required', 'string', 'size:64'],
            'method' => ['required', 'in:cash,card,qris'],
        ]);

        $result = DB::transaction(function () use ($data) {
            $table = Table::where('qr_code', $data['table_qr_code'])->lockForUpdate()->firstOrFail();
            $session = QrGuestSession::where('table_id', $table->id)
                ->where('token_hash', hash('sha256', $data['guest_session_token']))
                ->whereNull('closed_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (!$session) {
                return [
                    'error' => 'This ordering session has ended. Scan the table QR code again to start a new visit.',
                    'status' => 410,
                ];
            }

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
            return $this->error($result['error'], $result['status'] ?? 422);
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
            'qr_access_enabled' => true,
        ]);

        return $this->success(new TableResource($table), 'Table created.', 201);
    }

    public function show(Table $table): JsonResponse
    {
        return $this->success(new TableResource($table));
    }

    public function update(UpdateTableRequest $request, Table $table): JsonResponse
    {
        $data = $request->validated();
        $result = DB::transaction(function () use ($table, $data) {
            $table = Table::whereKey($table->id)->lockForUpdate()->firstOrFail();

            if (($data['qr_access_enabled'] ?? false) === true) {
                $hasOpenOrders = $table->orders()
                    ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                    ->exists();
                $hasActiveSession = QrGuestSession::where('table_id', $table->id)
                    ->whereNull('closed_at')
                    ->where('expires_at', '>', now())
                    ->exists();

                if ($table->status !== 'available' || $hasOpenOrders || $hasActiveSession) {
                    return ['error' => 'QR ordering can only be enabled for an available table with no active visit or open orders.'];
                }
            }

            if (isset($data['status']) && $data['status'] !== 'occupied') {
                $hasOpenOrders = $table->orders()
                    ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'served'])
                    ->exists();

                if ($hasOpenOrders) {
                    return ['error' => 'This table has an open order and must remain occupied.'];
                }
            }

            $hadActiveSession = QrGuestSession::where('table_id', $table->id)
                ->whereNull('closed_at')
                ->where('expires_at', '>', now())
                ->exists();

            $table->update($data);

            if (($data['status'] ?? null) === 'available') {
                QrGuestSession::where('table_id', $table->id)
                    ->whereNull('closed_at')
                    ->update(['closed_at' => now()]);

                if ($hadActiveSession) {
                    $table->update(['qr_access_enabled' => false]);
                }
            }

            return ['table' => $table->fresh()];
        });

        if (isset($result['error'])) {
            return $this->error($result['error'], 422);
        }

        return $this->success(new TableResource($result['table']), 'Table updated.');
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
