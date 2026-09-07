<?php

namespace App\Actions\Auth;

use App\DTOs\Auth\UpdateProfileDTO;
use App\Models\User;

class UpdateProfileAction
{
    public function execute(User $user, UpdateProfileDTO $dto): User
    {
        $user->update([
            'name' => $dto->name,
            'email' => $dto->email,
        ]);

        return $user;
    }
}
