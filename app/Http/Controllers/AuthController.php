<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;   

class AuthController extends Controller
{
    /**
    * Register User
    * 
    * @OA\Post(
    *   path="/api/register",
    *   tags={"Register"},
    *   operationId="register",
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/json",
    *           @OA\Schema(
    *               @OA\Property(
    *                   property="name",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="email",
    *                   type="string"
    *               ),
    *               @OA\Property(
    *                   property="password",
    *                   type="string"
    *               ),
    *               example={"name": "Krisna Cipta Raksa", "email": "admin@sera.com", "password": "new_password"}
    *           ),
    *       )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Respond if request success",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/ApiResponse"),
    *           @OA\Examples(example="result", value={"status": "success", "message": "User created successfully", "data": ""}, summary="Respond if request success")
    *       )
    *   ),
    *   @OA\Response(
    *       response=400,
    *       description="Respond if request failed",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/ApiResponse"),
    *           @OA\Examples(example="result_if_email_already_registered", value={"status": "error", "message": "Email already registered", "data": ""}, summary="Respond if request failed because email already registered"),
    *           @OA\Examples(example="result_if_any_mandatory_field_is_empty", value={"status": "error", "message": "Field required cannot be empty", "data": ""}, summary="Respond if request failed because any mandatory field is empty")
    *       )
    *   )
    * ) 
    */
    public function register(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        // check field is empty
        if (empty($name) || empty($email) || empty($password)) {
            return $this->response("error", "Field required cannot be empty", "", 400);
        }

        // check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response("error", "Please input valid email address", "", 400);
        }

        // check if password greater than 5 character        
        if (strlen($password) < 6) {
            return $this->response("error", "Minimal 6 character for password", "", 400);
        }

        // check if user already exists
        if (User::where('email', $email)->count() > 0) {
            return $this->response("error", "Email already registered", "", 400);
        }

        // // if all ok, let's create new user
        try {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = app('hash')->make($password);

            if ($user->save()) {
                return $this->response("success", "User created successfully", "", 200);
            }
        } catch (\Exception $e) {
            return $this->response("error", "Failed to create user", "", 400);
        }
    }

    /**
    * Login request to the API
    * 
    * @OA\POST(
    *   path="/api/login",
    *   tags={"Login"},
    *   operationId="login",
    *   @OA\RequestBody(
    *       @OA\MediaType(
    *           mediaType="application/json",
    *           @OA\Schema(
    *               @OA\Property(
    *                   property="email",
    *                   type="string",
    *               ),
    *               @OA\Property(
    *                   property="password",
    *                   type="string"
    *               ),
    *               example={"email": "kciptaraksa@gmail.com", "password": "qwerty"}
    *           )
    *       )
    *   ),
    *   @OA\Response(
    *       response=200,
    *       description="Successfully logged in",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/ApiResponse"),
    *           @OA\Examples(example="result", value={"status": "success",
    *               "message": "Successfully logged in",
    *               "data": {
    *                   "access_token": "token generated from api",
    *                   "token_type": "bearer",
    *                   "expires_in": 3600
    *               }}, summary="An result object while request is success")
    *       ),
    *   ),
    *   @OA\Response(
    *       response=400,
    *       description="Error show while any field required is empty",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/ApiResponse"),
    *           @OA\Examples(example="result", value={"status": "error", "message": "Field required cannot be empty", "data": ""}, summary="An result object while any field required is empty")
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       description="Error show while login attempt is failed",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/ApiResponse"),
    *           @OA\Examples(example="result", value={"status": "error", "message": "Unauthorized", "data": ""}, summary="An result object while login attempt is failed")
    *       )
    *   )
    * )
    */
    public function login(Request $request)
    {
        if (empty($request->password) || empty($request->email)) {
            return $this->response("error", "Field required cannot be empty", "", 400);
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return $this->response("error", "Unauthorized", '', 401);
        }
        return $this->response("success", "Successfully logged in", $this->respondWithToken($token)->original, 200);
    }

    /**
    *
    * Check login status user by token
    * 
    * @OA\Post(
    *   path="/api/logincheck",
    *   tags={"Check Login Status"},
    *   operationId="logincheck",
    *   security={{"apiAuth": {}}},
    *   @OA\Response(
    *       response=200,
    *       description="success",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/apiResponse"),
    *           @OA\Examples(
    *               example="result_if_user_loggedin",
    *               value={
    *                   "status": "success",
    *                   "message": "User is logged in",
    *                   "data": {
    *                       "is_logged_in": "true"
    *                   }
    *               },
    *               summary="Response while user is still logged in"
    *           ),
    *           @OA\Examples(
    *               example="result_if_user_not_loggedin",
    *               value={
    *                   "status": "error",
    *                   "message": "User is not logged in",
    *                   "data": {
    *                       "is_logged_in": "false"
    *                   }
    *               },
    *               summary="Response while user was not logged in"
    *           )
    *       )
    *   )
    * )
    * 
    */
    public function is_loggedin()
    {
        try {
            $user = auth()->userOrFail();
            return $this->response("success", "User is logged in", ["is_logged_id" => true], 200);
        } catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
            return $this->response("error", "User is not logged in", ["is_logged_id" => false], 200);
        }
        return $user;
    }

    /**
    *
    * Logout request from the API
    * 
    * @OA\Post(
    *   path="/api/logout",
    *   tags={"Logout"},
    *   operationId="logout",
    *   security={{"apiAuth": {}}},
    *   @OA\Response(
    *       response=200,
    *       description="success",
    *       @OA\JsonContent(
    *           type="object",
    *           @OA\Schema(ref="#/components/schemas/apiResponse"),
    *           @OA\Examples(
    *               example="result_if_user_loggedout",
    *               value={
    *                   "status": "success",
    *                   "message": "Successfully logged out",
    *                   "data": ""
    *               },
    *               summary="Response while user was logged out"
    *           )
    *       )
    *   ),
    *   @OA\Response(
    *       response=401,
    *       description="Response while user is not logged in",
    *       content={
    *           @OA\MediaType(
    *               mediaType="text/html",
    *               @OA\Schema(
    *                   example="Unauthorized."
    *               )
    *           )
    *       }
    *   )
    * )
    * 
    */
    public function logout()
    {
        auth()->logout();

        return $this->response("success", "Successfully logged out", "", 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    protected function response($status, $message, $data, $responseCode) 
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $responseCode);
    }
}
