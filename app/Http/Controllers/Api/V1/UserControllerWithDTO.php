<?php

namespace App\Http\Controllers\Api\V1;

use App\DTO\UserInDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Service\Interface\UserServiceInterface;
use Illuminate\Http\JsonResponse;


/**
 * @OA\Tag(
 *     name="Users",
 *     description="API Endpoints for users (DTO-based)"
 * )
 */
class UserControllerWithDTO extends Controller
{
    private UserServiceInterface $userService;
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Create a new user (DTO)
     *
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="secret")
     *         )
     *     ),
     *     @OA\Response(response=201, description="User created successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(UserInDTO $userInDTO):JsonResponse
    {
         $this->userService->create($userInDTO);
         return response()->json([
             'message' => 'User created successfully']);
    }


}
