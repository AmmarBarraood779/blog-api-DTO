<?php

namespace app\Actions\Categories;

use App\DTOs\Categories\CategoryDTO;
use App\Models\Category;
use Illuminate\Support\Str;

class CreateCategoryAction
{
    public function execute(CategoryDTO $dto): Category
    {
        return Category::create([
            'name' => $dto->name,
            'slug' => Str::slug($dto->name).'-'.Str::random(5),
        ]);
    }
}
