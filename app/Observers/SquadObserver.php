<?php

namespace App\Observers;

use App\Models\Squad;
use Illuminate\Support\Facades\Log;

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
        Log::info('Squad created', [
            'id' => $squad->name,
            'code' => $squad->code
        ]);
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
        Log::info('Squad created', [
            'id' => $squad->name,
            'code' => $squad->code
        ]);
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
