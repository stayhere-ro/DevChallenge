<?php

namespace App\Http\Service\Impl\UserImpl;

use App\DTO\UserInDTO;
use App\DTO\UserOutDTO;
use App\Http\Repository\Interface\UserRepositoryInterface;
use App\Http\Service\Interface\UserServiceInterface;

class UserServiceImplWithDTO implements UserServiceInterface
{

    private UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(UserInDTO $userDTO): UserOutDTO
    {

        return $this->userRepository->create($userDTO);
    }
}
