<?php

namespace App\Actions\Categories;

use App\DTOs\Categories\CategoryDTO;
use App\Models\Category;
use Illuminate\Support\Str;

class UpdateCategoryAction
{
    public function execute(Category $category, CategoryDTO $dto): Category
    {
        $category->update([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name).'-'.Str::random(5),
        ]);

        return $category;
    }
}
