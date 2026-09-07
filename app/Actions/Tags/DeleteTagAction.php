<?php

namespace App\Actions\Categories;

use App\Models\Tag;

class DeleteTagAction
{
    public function execute(Tag $tag): void
    {
        $tag->delete();
    }
}
