<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Modifier\StoreModifierRequest;
use App\Http\Requests\Modifier\UpdateModifierRequest;
use App\Http\Resources\ModifierResource;
use App\Models\MenuItem;
use App\Models\Modifier;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ModifierController extends Controller
{
    use ApiResponse;

    public function index(MenuItem $menuItem): JsonResponse
    {
        $modifiers = $menuItem->modifiers()->orderBy('name')->get();

        return $this->success(ModifierResource::collection($modifiers));
    }

    public function store(StoreModifierRequest $request, MenuItem $menuItem): JsonResponse
    {
        $modifier = $menuItem->modifiers()->create([
            'name'         => $request->name,
            'type'         => $request->type,
            'price_delta'  => $request->price_delta ?? 0,
            'is_available' => $request->is_available ?? true,
        ]);

        return $this->success(new ModifierResource($modifier), 'Modifier created.', 201);
    }

    public function update(UpdateModifierRequest $request, MenuItem $menuItem, Modifier $modifier): JsonResponse
    {
        $modifier->update($request->only(['name', 'type', 'price_delta', 'is_available']));

        return $this->success(new ModifierResource($modifier), 'Modifier updated.');
    }

    public function destroy(MenuItem $menuItem, Modifier $modifier): JsonResponse
    {
        $modifier->delete();

        return $this->success(null, 'Modifier deleted.');
    }
}
