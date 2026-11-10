<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $categories = Category::withCount('menuItems')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return $this->success(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = Category::create([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? 0,
            'is_active'  => $request->is_active ?? true,
        ]);

        return $this->success(new CategoryResource($category), 'Category created.', 201);
    }

    public function show(Category $category): JsonResponse
    {
        return $this->success(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->only(['sort_order', 'is_active']);

        if ($request->has('name')) {
            $data['name'] = $request->name;
            $data['slug'] = Str::slug($request->name);
        }

        $category->update($data);

        return $this->success(new CategoryResource($category), 'Category updated.');
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return $this->success(null, 'Category deleted.');
    }
}
