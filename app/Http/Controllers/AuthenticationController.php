<?php

namespace App\Http\Controllers;

use App\Dto\UserAuthenticationDto;
use App\Dto\UserRegistrationDto;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserServiceInterface;

class AuthenticationController extends ApiController
{
    public function __construct(
        public readonly UserServiceInterface $userService
    ) {
    }

    public function register(UserRegistrationRequest $request)
    {
        $validatedData = UserRegistrationDto::fromRequest($request->validated());

        [$user, $token] = $this->userService->saveUser($validatedData);

        return $this->respond([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $validatedData = UserAuthenticationDto::fromRequest($request->validated());

        [$user, $token] = $this->userService->authenticateUser($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'token'  => $token,
            ],
        ]);
    }
}
