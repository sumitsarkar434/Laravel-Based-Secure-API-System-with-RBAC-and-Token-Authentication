<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
             ->postJson('/api/v1/posts', [
                 'title' => 'Hello World',
                 'body'  => 'My first post.',
             ])
             ->assertStatus(201)
             ->assertJsonFragment(['title' => 'Hello World']);
    }

    public function test_user_can_list_own_posts(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        Post::factory()->count(3)->create(['user_id' => $user->id]);
        Post::factory()->count(2)->create(['user_id' => $other->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/posts');

        $response->assertOk();
        $this->assertCount(3, $response->json('data'));
    }

    public function test_user_cannot_update_others_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post  = Post::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other, 'sanctum')
             ->putJson("/api/v1/posts/{$post->id}", ['title' => 'Hacked'])
             ->assertStatus(403);
    }

    public function test_admin_can_see_all_posts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create()->each(function ($user) {
            Post::factory()->count(2)->create(['user_id' => $user->id]);
        });

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/v1/posts');

        $response->assertOk();
        $this->assertCount(4, $response->json('data'));
    }
}
