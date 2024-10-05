<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use OpenApi\Annotations as OA;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;

/**
 * @OA\Schema(
 *     schema="User",
 *     title="User Model",
 *     description="A user model",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string", format="email"),
 *     @OA\Property(property="password", type="string"),
 * )
 */

class AuthController extends Controller {
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * @OA\Post(
     *     path="/v1/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     description="This endpoint allows you to register a new user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function register(Request $request) {

        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'status' => self::STATUS_SUCCESS,
            'message' => 'User registered successfully.',
            'data' => ['token' => $token, 'user' => $user],
        ], 201);
        die;
    }

    /**
     * @OA\Post(
     *     path="/v1/login",
     *     tags={"Auth"},
     *     summary="Login a user",
     *     description="This endpoint allows you to login a user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('Personal Access Token')->accessToken;

            return response()->json([
                'status' => self::STATUS_SUCCESS,
                'message' => 'Login successful.',
                'data' => ['token' => $token, 'user' => $user],
            ]);
        }

        return response()->json([
            'status' => self::STATUS_FAILED,
            'message' => 'Unauthorized',
        ], 401);
    }

    /**
     * @OA\Get(
     *      path="/v1/user",
     *      tags={"Auth"},
     *      summary="Get authenticated user",
     *      description="This endpoint returns the authenticated user.",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(response=200, description="User retrieved successfully"),
     *      @OA\Response(response=401, description="Unauthorized")
     * )
    */
    public function getUser(Request $request) {
        return response()->json(['user' => auth()->user()]);
    }
}
