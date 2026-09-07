<?php

namespace App\Http\Controllers\Api;

use App\Actions\Categories\CreateCategoryAction;
use App\Actions\Categories\DeleteCategoryAction;
use App\Actions\Categories\UpdateCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::latest()->get();

        return CategoryResource::collection($categories);
    }

    public function store(StoreCategoryRequest $request, CreateCategoryAction $action): JsonResponse
    {
        $category = $action->execute($request->toDTO());

        return response()->json([
            'message' => 'تم إنشاء التصنيف بنجاح.',
            'category' => new CategoryResource($category),
        ], 201);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category, UpdateCategoryAction $action): JsonResponse
    {
        $updatedCategory = $action->execute($category, $request->toDTO());

        return response()->json([
            'message' => 'تم تحديث التصنيف بنجاح.',
            'category' => new CategoryResource($updatedCategory),
        ]);
    }

    public function destroy(Category $category, DeleteCategoryAction $action): JsonResponse
    {
        $action->execute($category);

        return response()->json([
            'message' => 'تم حذف التصنيف بنجاح.',
        ]);
    }
}
