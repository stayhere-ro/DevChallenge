<?php

namespace App\Http\Repository\RepositoryImpl\EloqImpl;

use App\DTO\UserInDTO;
use App\DTO\UserOutDTO;
use App\Http\Repository\Interface\UserRepositoryInterface;
use App\Models\User;

class UserRepositoryWithDTO implements UserRepositoryInterface
{

    public function create(UserInDTO $userDTO): UserOutDTO
    {

        return UserOutDTO::from(User::create($userDTO->toArray()));
    }
}
