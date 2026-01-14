<?php

namespace App\Http\Repository\Interface;

use App\DTO\UserInDTO;
use App\DTO\UserOutDTO;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
   public function create(UserInDTO $userDTO):UserOutDTO;


}
