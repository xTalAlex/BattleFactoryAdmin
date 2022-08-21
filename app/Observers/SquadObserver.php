<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Squad;
use App\Notifications\SquadSubmitted;
use Illuminate\Support\Facades\Notification;

class SquadObserver
{
    /**
     * Handle the Squad "created" event.
     *
     * @param  \App\Models\Squad  $squad
     * @return void
     */
    public function created(Squad $squad)
    {
        Notification::send( User::where('is_admin',true)->get() , new SquadSubmitted($squad) );
    }

    /**
     * Handle the Squad "updated" event.
     *
     * @param  \App\Models\Squad  $squad
     * @return void
     */
    public function updated(Squad $squad)
    {
        //
    }

    /**
     * Handle the Squad "deleted" event.
     *
     * @param  \App\Models\Squad  $squad
     * @return void
     */
    public function deleted(Squad $squad)
    {
        //
    }

    /**
     * Handle the Squad "restored" event.
     *
     * @param  \App\Models\Squad  $squad
     * @return void
     */
    public function restored(Squad $squad)
    {
        //
    }

    /**
     * Handle the Squad "force deleted" event.
     *
     * @param  \App\Models\Squad  $squad
     * @return void
     */
    public function forceDeleted(Squad $squad)
    {
        //
    }
}
