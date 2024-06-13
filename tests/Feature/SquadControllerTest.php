<?php

namespace Tests\Feature;

use App\Models\Squad;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SquadControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_visitors_can_see_squad_index()
    {
        $response = $this->getJson('api/squads');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_visitors_can_submit_squad_with_email()
    {
        $user = User::factory()->make();
        $squad = Squad::factory(1)->make(['email' => $user->email])->first();

        $response = $this->postJson('api/squads', $squad->toArray());

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', $squad->name);

        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }

    /**
     * @return void
     */
    public function test_visitors_can_submit_squad_without_email()
    {
        $user = User::factory()->make();
        $squad = Squad::factory(1)->make(['email' => null])->first();

        $response = $this->postJson('api/squads', $squad->toArray());

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', $squad->name);

        $this->assertDatabaseHas('squads', [
            'name' => $squad->name,
            'user_id' => null,
        ]);
    }
}
