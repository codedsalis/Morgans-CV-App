<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Profile;
use App\Models\Resume;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_user_can_upload_a_resume()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user);

        $path = __DIR__ . '/test-resume.pdf';
        $file = new UploadedFile($path, 'test-resume.pdf', 'application/pdf', null, true);
        $requestData = ['resume' => $file];

        $response = $this->postJson(route('upload-cv'), $requestData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'data'])
            ->assertJson(['status' => 'success']);
    }

    public function test_a_user_can_view_his_resume()
    {
        $user = User::factory()->create();

        $path = __DIR__ . '/test-resume.pdf';
        $file = new UploadedFile($path, 'test-resume.pdf', 'application/pdf', null, true);
        $requestData = ['resume' => $file];

        $response = $this->postJson(route('upload-cv'), $requestData);

        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'cv_path' => $path,
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('show-cv', ['id' => $profile->id]));

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status', 'data'])
            ->assertJson(['status' => 'success']);
    }

    public function test_viewing_a_non_existing_resume_returns_a_not_found_response()
    {
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('show-cv', ['id' => 9999999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure(['status', 'data'])
            ->assertJson(['status' => 'failed']);
    }
}
