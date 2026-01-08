<?php

namespace App\Http\Service\Interface;

use App\DTO\UserInDTO;
use App\DTO\UserOutDTO;

interface UserServiceInterface
{
    public function create(UserInDTO $userDTO):UserOutDTO;

}
