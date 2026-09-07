<?php

namespace app\Actions\Tags;

use app\DTOs\Tags\TagDTO;
use App\Models\Tag;
use illuminate\Support\Str;

class CreateTagAction
{
    public function execute(TagDTO $dto): Tag
    {
        return Tag::create([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name).'-'.Str::random(5),
        ]);
    }
}
