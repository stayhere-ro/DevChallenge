<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Hairdresser Booking App",
 *     version="1.0.0",
 *     description="This is a web application for hairdresser booking",
 * )
 *
 * @OA\Server(
 *     url="http://devchallenge.test",
 *     description="Local Development Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
