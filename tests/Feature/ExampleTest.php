<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();
        Client::factory()->for($user)->create();
        $response = $this->actingAs($user)
            ->withSession(['foo' => 'bar'])
            ->get('/');
        $response->assertStatus(200);
    }
}
