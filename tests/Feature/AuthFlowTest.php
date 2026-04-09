<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_sign_up_with_a_profile_photo_and_is_redirected_to_onboarding(): void
    {
        Storage::fake('public');

        $response = $this->post('/signup', [
            'name' => 'New Member',
            'email' => 'new-member@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'teach-and-learn',
            'profile_photo_upload' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect('/onboarding');
        $this->assertAuthenticated();

        $user = User::where('email', 'new-member@example.com')->firstOrFail();

        $this->assertSame('New Member', $user->name);
        $this->assertNotNull($user->profile_photo);
        Storage::disk('public')->assertExists($user->profile_photo);
    }

    public function test_a_user_can_log_in_with_valid_credentials(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'teach-and-learn',
            'teach_skills' => [],
            'learn_skills' => [],
            'formats' => [],
            'portfolio_links' => [],
            'saved_users' => [],
            'onboarding_completed' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/onboarding');
        $this->assertAuthenticated();
    }

    public function test_a_logged_in_user_can_replace_their_profile_photo_in_settings(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'role' => 'teach-and-learn',
            'teach_skills' => ['React'],
            'learn_skills' => ['Figma'],
            'formats' => ['video call'],
            'portfolio_links' => [],
            'saved_users' => [],
            'timezone' => 'Asia/Manila',
            'availability' => 'Weeknights',
            'onboarding_completed' => true,
        ]);

        $response = $this->actingAs($user)->post('/settings', [
            'bio' => 'Updated bio',
            'location' => 'Manila',
            'timezone' => 'Asia/Manila',
            'availability' => 'Weeknights',
            'teach_skills' => 'React, JavaScript',
            'learn_skills' => 'Figma',
            'portfolio_links' => '',
            'formats' => ['video call', 'chat'],
            'profile_photo_upload' => UploadedFile::fake()->image('new-avatar.png'),
        ]);

        $response->assertRedirect();

        $user->refresh();

        $this->assertNotNull($user->profile_photo);
        Storage::disk('public')->assertExists($user->profile_photo);
    }
}
