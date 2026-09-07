<?php

namespace App\Http\Controllers\Api;

use App\Actions\Categories\DeleteTagAction;
use App\Actions\Categories\UpdateTagAction;
use App\Actions\Tags\CreateTagAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Categories\UpdateTagRequest;
use App\Http\Requests\Tags\StoreTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $tags = Tag::latest()->get();

        return TagResource::collection($tags);
    }

    public function store(StoreTagRequest $request, CreateTagAction $action): JsonResponse
    {
        $tag = $action->execute($request->toDTO());

        return response()->json([
            'message' => 'تم إنشاء الوسم بنجاح.',
            'tag' => new TagResource($tag),
        ], 201);
    }

    public function show(Tag $tag): TagResource
    {
        return new TagResource($tag);
    }

    public function update(UpdateTagRequest $request, Tag $tag, UpdateTagAction $action): JsonResponse
    {
        $updatedTag = $action->execute($tag, $request->toDTO());

        return response()->json([
            'message' => 'تم تحديث التصنيف بنجاح.',
            'category' => new TagResource($updatedTag),
        ]);
    }

    public function destroy(Tag $tag, DeleteTagAction $action): JsonResponse
    {
        $action->execute($tag);

        return response()->json([
            'message' => 'تم حذف التصنيف بنجاح.',
        ]);
    }
}
