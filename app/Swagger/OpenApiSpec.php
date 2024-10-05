<?php

namespace App\Swagger; // Adjust namespace if necessary

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="My API",
 *     version="1.0.0",
 *     description="API documentation for my application",
 *     @OA\Contact(name="API Support", email="support@example.com")
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Development server"
 * )
 */
class OpenApiSpec {}