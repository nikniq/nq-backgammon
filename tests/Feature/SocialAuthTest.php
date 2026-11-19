<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.google.client_id' => 'gid', 'services.nikniq.client_id' => 'nid']);
    }

    private function fakeProvider(string $provider, string $id, string $email): void
    {
        $user = (new SocialUser)->map(['id' => $id, 'name' => 'Ada Player', 'email' => $email, 'avatar' => 'https://example.com/a.png']);
        $driver = Mockery::mock();
        $driver->shouldReceive('user')->andReturn($user);
        Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);
    }

    public function test_pages_render(): void
    {
        foreach (['/', '/play', '/pricing', '/leaderboard', '/login', '/register', '/privacy', '/terms'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_google_callback_creates_and_logs_in_user(): void
    {
        $this->fakeProvider('google', 'g-123', 'ada@example.com');

        $this->get('/auth/google/callback')->assertRedirect(route('play'));

        $user = User::where('email', 'ada@example.com')->firstOrFail();
        $this->assertSame('g-123', $user->google_id);
        $this->assertNull($user->password);
        $this->assertAuthenticatedAs($user);
    }

    public function test_nikniq_callback_links_existing_email_account(): void
    {
        $existing = User::factory()->create(['email' => 'ada@example.com']);
        $this->fakeProvider('nikniq', 'n-9', 'ada@example.com');

        $this->get('/auth/nikniq/callback')->assertRedirect(route('play'));

        $this->assertSame('n-9', $existing->fresh()->nikniq_id);
        $this->assertSame(1, User::count());
        $this->assertAuthenticatedAs($existing);
    }

    public function test_unknown_or_unconfigured_provider_404s(): void
    {
        $this->get('/auth/facebook/redirect')->assertNotFound();
        config(['services.google.client_id' => null]);
        $this->get('/auth/google/redirect')->assertNotFound();
    }
}
