<?php

namespace App\Interfaces;

use App\Dto\ResumeUploadDto;
use App\Models\Profile;

interface ProfileServiceInterface
{
    public function saveResume(ResumeUploadDto $data): Profile;

    public function findResume(string $id): ?Profile;
}
