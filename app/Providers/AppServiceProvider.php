<?php

namespace App\Providers;

use App\Repositories\PokemonRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //

        $this->app->bind(PokemonRepository::class, function ($app) {
            $cache = $this->app->make(Cache::class);
            $client = new Client();
            return new PokemonRepository($cache, $client, config('services.pokemon.base_url'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        JsonResource::withoutWrapping();
    }
}
