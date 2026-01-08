<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\UserInDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Service\Interface\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserControllerWithDTO extends Controller
{
    private UserServiceInterface $userService;
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    public function store(UserInDTO $userInDTO):JsonResponse
    {
         $this->userService->create($userInDTO);
         return response()->json([
             'message' => 'User created successfully']);
    }


}
