<?php

namespace App\Http\Controllers;

use App\Events\ResumeUploadEvent;
use App\Http\Requests\UploadCvRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;

use function PHPUnit\Framework\matches;

class ProfileController extends ApiController
{
    public function __construct(
        public readonly Parser $pdfPerser
    ) {
    }

    public function uploadCv(UploadCvRequest $request)
    {
        if ($request->user()->cannot('create', Profile::class)) {
            return $this->respond([
                'status' => 'failed',
                'message' => 'You are not allowed to create'
            ]);
        }

        $validatedData = $request->validated();

        $path = $request->file('resume')->store('pdfs');

        $file = storage_path('app/' . $path);

        $resume = Profile::query()
            ->create([
                'user_id' => $request->user()->id,
                'cv_path' => $file,
            ]);

        ResumeUploadEvent::dispatch($resume);

        return $this->respond([
            'status' => 'success',
            'data' => $resume,
        ]);
    }

    public function show($id, Request $request)
    {
        try {
            $profile = Profile::query()
                ->where('id', $id)
                ->firstOrFail();

            return $this->respond([
                'status' => 'success',
                'data' => $profile,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failed',
                'data' => [
                    'message' => 'The request resume is not found',
                ]
            ], 404);
        }
    }
}
