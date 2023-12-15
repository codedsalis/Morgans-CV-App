<?php

namespace App\Interfaces;

use App\Dto\UserAuthenticationDto;
use App\Dto\UserRegistrationDto;

interface UserServiceInterface
{
    public function saveUser(UserRegistrationDto $data): array;

    public function authenticateUser(UserAuthenticationDto $userData): array;
}
