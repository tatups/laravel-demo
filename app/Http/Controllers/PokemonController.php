<?php

namespace App\Http\Controllers;

use App\Events\PokemonViewed;
use App\Http\Resources\PokemonResource;
use App\Models\PokemonView;
use App\Notifications\Pokemon\CongratulationsNotification;
use App\Repositories\PokemonRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

/**
 * @see \Tests\Feature\PokemonControllerTest
 */
class PokemonController extends Controller
{
    private PokemonRepository $repository;

    public function __construct(PokemonRepository $repository)
    {
        $this->repository = $repository;
    }

    //
    public function show(Request $request, string $pokemon)
    {
        $data = $this->repository->show($pokemon);

        PokemonView::query()->create([
            'pokemon_name' => $pokemon,
            'user_id' => $request->user()->id,
        ]);

        Event::dispatch(new PokemonViewed($request->user(), $pokemon));

        return new PokemonResource($data);
    }
}
