<?php

namespace App\Http\Service\Impl\Booking;

use App\DTO\BookingInDTO;
use App\DTO\UserInDTO;
use App\Http\Repository\Interface\BookingRepositoryInterface;
use App\Http\Service\Interface\BookingServiceInterface;

class BookingServiceImplWithDTO implements BookingServiceInterface
{
    private BookingRepositoryInterface $bookingRepository;
    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }


    public function create(BookingInDTO $bookingDTO, UserInDTO $userInDTO)
    {
        $this->bookingRepository->create($bookingDTO,$userInDTO);
    }
}
