<?php

namespace App\Http\Controllers;

use App\Dto\ResumeUploadDto;
use App\Http\Requests\UploadCvRequest;
use App\Interfaces\ProfileServiceInterface;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends ApiController
{
    public function __construct(
        public readonly ProfileServiceInterface $profileService
    ) {
    }

    public function uploadCv(UploadCvRequest $request): JsonResponse
    {
        if ($request->user()->cannot('create', Profile::class)) {
            return $this->respond([
                'status' => 'failed',
                'message' => 'You are not allowed to create a profile'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validatedData = ResumeUploadDto::fromRequest($request->validated());

        $this->authorize('create', Profile::class);

        $resume = $this->profileService->saveResume($validatedData);

        return $this->respond([
            'status' => 'success',
            'data' => $resume,
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $resume = $this->profileService->findResume($id);

        if ($resume === null) {
            return response()->json([
                'status' => 'failed',
                'data' => [
                    'message' => 'The requested resume is not found',
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        $this->authorize('view', $resume);

        return $this->respond([
            'status' => 'success',
            'data' => $resume,
        ]);
    }
}
