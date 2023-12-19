<?php

namespace App\Listeners;

use App\Events\PokemonViewed;
use App\Models\PokemonView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class PokemonViewedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PokemonViewed $event): void
    {
        //
        $viewCount = PokemonView::where('pokemon_name', $event->pokemonName)->count();

        if ($viewCount === 5) {
            Notification::send($event->user, new CongratulationsNotification($event->pokemonName, 5));
        }
    }
}
