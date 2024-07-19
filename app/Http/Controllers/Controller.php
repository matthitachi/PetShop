<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Pet Shop Api",
 *      description="All Apis for Pet shop"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 *     description="API Key Authentication",
 *     name="Authorization",
 *     in="header",
 * )
 */
abstract class Controller
{
    //
}
