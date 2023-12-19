<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Support\Facades\Http;

class PokemonRepository
{
    public const CACHE_KEY = 'pokemons';
    public const CACHE_TTL = 60;

    private Cache $cache;
    private ClientInterface $client;
    private $baseUrl;

    public function __construct(Cache $cache, ClientInterface $client, string $baseUrl)
    {
        $this->cache = $cache;
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    public function show(string $pokemon): object
    {
        $cacheKey = self::CACHE_KEY . $pokemon;

        if ($this->cache->has($cacheKey)) {
            $data = json_decode($this->cache->get($cacheKey), false);
        } else {
            $url =  $this->baseUrl . $pokemon;

            $data = $this->client->request('GET', $url)->getBody()->getContents();
            $data = json_decode($data, false);

            $this->cache->put($cacheKey, json_encode($data), self::CACHE_TTL);
        }

        return $data;
    }
}
