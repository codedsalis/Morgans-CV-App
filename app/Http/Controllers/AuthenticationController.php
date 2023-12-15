<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends ApiController
{
    public function register(UserRegistrationRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::query()
            ->create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
            ]);

        $token = $user->createToken("$user->name token")->plainTextToken;

        return $this->respond([
            'user' => $user,
            'token' => $token,
        ]);

        $token = $user->createToken("$user->name token")->plainTextToken;
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'email|string|required',
            'password' => 'required|string'
        ]);

        $user = User::query()
            ->where('email', $validatedData['email'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => ['The provided credentials are incorrect.'],
            ]);
        }

        //Delete all current tokens
        $user->tokens()->delete();

        $token = $user->createToken("$user->name token")->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => new UserResource($user),
                'token'  => $token,
            ],
        ]);
    }
}
