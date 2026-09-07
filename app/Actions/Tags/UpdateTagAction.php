<?php

namespace App\Actions\Categories;

use App\DTOs\Tags\TagDTO;
use App\Models\Tag;
use Illuminate\Support\Str;

class UpdateTagAction
{
    public function execute(Tag $tag, TagDTO $dto): Tag
    {
        $tag->update([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name).'-'.Str::random(5),
        ]);

        return $tag;
    }
}
