<?php

namespace App\Services;

use App\Dto\ResumeUploadDto;
use App\Events\ResumeUploadEvent;
use App\Interfaces\ProfileServiceInterface;
use App\Models\Profile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class ProfileService implements ProfileServiceInterface
{
    public function saveResume(ResumeUploadDto $profileData): Profile
    {
        $path = Storage::put('pdfs', $profileData->resume);

        $file = storage_path('app/' . $path);

        $resume = Profile::query()
            ->create([
                'user_id' => auth()->user()->id,
                'cv_path' => $file,
            ]);

        ResumeUploadEvent::dispatch($resume);

        return $resume;
    }

    public function findResume(string $id): ?Profile
    {
        try {
            $profile = Profile::query()
                ->where('id', $id)
                ->firstOrFail();

            return $profile;
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}
