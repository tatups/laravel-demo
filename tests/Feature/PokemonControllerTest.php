<?php

namespace Tests\Feature;

use App\Notifications\Pokemon\CongratulationsNotification;
use App\Repositories\PokemonRepository;
use Database\Factories\PokemonViewFactory;
use Database\Factories\UserFactory;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PokemonController
 */
class PokemonControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_pokemon(): void
    {
        $user = UserFactory::new()->create(['name' => 'Tatu']);

        $mockPoke = [
            'name' => 'ditto',
        ];


        // Http::fake([
        //     'https://pokeapi.co/api/v2/pokemon/ditto' => Http::response($mockPoke, 200),
        // ]);

        // $data = $this->client->request('GET', $url)->getBody()->getContents();

        $mockClient = Mockery::mock(ClientInterface::class, function ($mock) use ($mockPoke) {
            $mock->shouldReceive('request')
                ->once()
                ->with('GET', 'test' . $mockPoke['name'])
                ->andReturn(
                    new Response(200, [], json_encode($mockPoke))
                );
        });

        $cache = $this->app->make(\Illuminate\Cache\Repository::class);


        $this->app->instance(PokemonRepository::class, new PokemonRepository($cache, $mockClient, 'test'));


        $this->actingAs($user);

        $response = $this->get('/pokemons/' . $mockPoke['name']);

        $response->assertStatus(200);

        $response->assertJson($mockPoke);

        $this->assertDatabaseHas('pokemon_views', [
            'pokemon_name' => $mockPoke['name'],
            'user_id' => $user->id,
        ]);
    }

    public function test_nth_view_sends_congratulations(): void
    {
        Notification::fake();
        $user = UserFactory::new()->create(['name' => 'Tatu']);

        PokemonViewFactory::new()->count(4)->create([
            'user_id' => $user->id,
            'pokemon_name' => 'ditto',
        ]);

        $mockPoke = [
            'name' => 'ditto',
        ];

        Http::fake([
            'https://pokeapi.co/api/v2/pokemon/ditto' => Http::response($mockPoke, 200),
        ]);

        $this->actingAs($user);

        $response = $this->get('/pokemons/' . $mockPoke['name']);

        $response->assertStatus(200);

        $response->assertJson($mockPoke);

        $this->assertDatabaseCount('pokemon_views', 5);

        Notification::assertSentTo($user, CongratulationsNotification::class, function (CongratulationsNotification $notifiable) {
            return $notifiable->pokemon === 'ditto';
        });
    }
}
