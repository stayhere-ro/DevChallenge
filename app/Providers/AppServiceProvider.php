<?php

namespace App\Providers;

use App\Http\Repository\Interface\BookingRepositoryInterface;
use App\Http\Repository\Interface\HairdresserRepositoryInterface;
use App\Http\Repository\Interface\UserRepositoryInterface;
use App\Http\Repository\RepositoryImpl\EloqImpl\BookingRepositoryWithDTO;
use App\Http\Repository\RepositoryImpl\EloqImpl\HairdresserRepositoryWithDTO;
use App\Http\Repository\RepositoryImpl\EloqImpl\UserRepositoryWithDTO;
use App\Http\Service\Impl\Booking\BookingServiceImplWithDTO;
use App\Http\Service\Impl\Hairdresser\HairdresserServiceImplWithDTO;
use App\Http\Service\Impl\UserImpl\UserServiceImplWithDTO;
use App\Http\Service\Interface\BookingServiceInterface;
use App\Http\Service\Interface\HairdresserServiceInterface;
use App\Http\Service\Interface\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // register repository
        $this->app->bind(UserRepositoryInterface::class, UserRepositoryWithDTO::class);
        $this->app->bind(HairdresserRepositoryInterface::class, HairdresserRepositoryWithDTO::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepositoryWithDTO::class);




        // register service
        $this->app->bind(UserServiceInterface::class,UserServiceImplWithDTO::class);
        $this->app->bind(HairdresserServiceInterface::class,HairdresserServiceImplWithDTO::class);
        $this->app->bind(BookingServiceInterface::class,BookingServiceImplWithDTO::class);


    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
