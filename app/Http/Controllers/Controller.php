<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @OA\Info(
     *   title="Example API",
     *   version="1.0",
     *   @OA\Contact(
     *     email="kciptaraksa@gmail.com",
     *     name="Krisna Cipta Raksa"
     *   )
     * ),
     * @OA\SecurityScheme(
     *   type="http",
     *   description="Login with email and password to get authentication token",
     *   in="header",
     *   scheme="bearer",
     *   bearerFormat="JWT",
     *   securityScheme="apiAuth",
     *   name="apiAuth"
     * )
    */ 
}
