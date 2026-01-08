<?php
namespace App\Http\Controllers;
use OpenApi\Attributes as OA;




#[OA\Info(title: "My Laravel API", version: "1.0.0", description: "API documentation")]
#[OA\Server(url: "http://localhost:8000", description: "Local server")]
#[OA\SecurityScheme(securityScheme: "bearerAuth", type: "http", scheme: "bearer")]
//#[OA\Tag(name: "Auth")]
//#[OA\Tag(name: "Users")]
//#[OA\Tag(name: "Bookings")]
//#[OA\Tag(name: "Hairdressers")]
class OpenApiInfo
{
    // This class only exists to hold OpenAPI attributes for swagger-php scanning.
}
