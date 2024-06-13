<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\UserCreated;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @return void
     */
    public function created(User $user)
    {
        //$user->notify(new UserCreated());
        Log::info('User created', [
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Handle the User "deleting" event.
     *
     * @return void
     */
    public function deleting(User $user)
    {
        $user->squads()->delete();
    }

    /**
     * Handle the User "deleted" event.
     *
     * @return void
     */
    public function deleted(User $user)
    {
        Log::info('User deleted', [
            'id' => $user->id,
            'name' => $user->name,
        ]);
    }

    /**
     * Handle the User "restored" event.
     *
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
