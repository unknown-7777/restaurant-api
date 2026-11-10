<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\MenuItem\UpdateMenuItemRequest;
use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = MenuItem::with('category')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        if ($request->has('search')) {
            $query->where('name', 'ilike', '%' . $request->search . '%');
        }

        $items = $query->paginate($request->get('per_page', 15));

        return $this->success([
            'items' => MenuItemResource::collection($items),
            'meta'  => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    public function store(StoreMenuItemRequest $request): JsonResponse
    {
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu-items', 'public');
        }

        $item = MenuItem::create([
            'category_id'  => $request->category_id,
            'name'         => $request->name,
            'slug'         => Str::slug($request->name),
            'description'  => $request->description,
            'price'        => $request->price,
            'image_path'   => $imagePath,
            'is_available' => $request->is_available ?? true,
            'sort_order'   => $request->sort_order ?? 0,
        ]);

        $item->load('category');

        return $this->success(new MenuItemResource($item), 'Menu item created.', 201);
    }

    public function show(MenuItem $menuItem): JsonResponse
    {
        $menuItem->load(['category', 'modifiers']);

        return $this->success(new MenuItemResource($menuItem));
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): JsonResponse
    {
        $data = $request->only([
            'category_id', 'description',
            'price', 'is_available', 'sort_order',
        ]);

        if ($request->has('name')) {
            $data['name'] = $request->name;
            $data['slug'] = Str::slug($request->name);
        }

        if ($request->hasFile('image')) {
            if ($menuItem->image_path) {
                Storage::disk('public')->delete($menuItem->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }

        $menuItem->update($data);
        $menuItem->load('category');

        return $this->success(new MenuItemResource($menuItem), 'Menu item updated.');
    }

    public function destroy(MenuItem $menuItem): JsonResponse
    {
        if ($menuItem->image_path) {
            Storage::disk('public')->delete($menuItem->image_path);
        }

        $menuItem->delete();

        return $this->success(null, 'Menu item deleted.');
    }
}
