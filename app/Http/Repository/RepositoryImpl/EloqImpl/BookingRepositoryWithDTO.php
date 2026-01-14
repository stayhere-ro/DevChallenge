<?php

namespace App\Http\Repository\RepositoryImpl\EloqImpl;

use App\DTO\BookingInDTO;
use App\DTO\BookingOutDTO;
use App\DTO\UserInDTO;
use App\Http\Repository\Interface\BookingRepositoryInterface;
use App\Models\Booking;
use App\Models\User;

class BookingRepositoryWithDTO implements BookingRepositoryInterface
{
    private UserRepositoryWithDTO $userRepository;

    public function __construct(UserRepositoryWithDTO $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    public function create(BookingInDTO $bookingDTO,UserInDTO $userInDTO)
    {
        $userInDTO =new UserInDTO(
            name: $userInDTO->name,
            email: $userInDTO->email,
            password: 'password',
        );
        $user = $this->userRepository->create($userInDTO);
        $booking=Booking::create([
            'user_id' => $user->id,
            'date' => $bookingDTO->date,
            'time' => $bookingDTO->hour,
        ]);
        $booking->hairdresser()->attach($bookingDTO->hairdresser_id);













    }
}
