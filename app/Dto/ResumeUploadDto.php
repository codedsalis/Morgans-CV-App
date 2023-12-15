<?php

namespace App\Dto;

use Illuminate\Http\UploadedFile;

class ResumeUploadDto
{
    public function __construct(
        public readonly UploadedFile $resume,
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            resume: $data['resume'],
        );
    }
}
