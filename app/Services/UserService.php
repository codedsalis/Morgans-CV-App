<?php

namespace App\Services;

use App\Dto\UserAuthenticationDto;
use App\Dto\UserRegistrationDto;
use App\Interfaces\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService implements UserServiceInterface
{
    public function saveUser(UserRegistrationDto $data): array
    {
        $user = User::query()
            ->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

        $token = $user->createToken("$user->name token")->plainTextToken;

        return [$user, $token];
    }

    public function authenticateUser(UserAuthenticationDto $userData): array
    {
        $user = User::query()
            ->where('email', $userData->email)
            ->first();

        if (!$user || !Hash::check($userData->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect.'],
            ]);
        }

        //Delete all current tokens
        $user->tokens()->delete();

        $token = $user->createToken("$user->name token")->plainTextToken;

        return [$user, $token];
    }
}
