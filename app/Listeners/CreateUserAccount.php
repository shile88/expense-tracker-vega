<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\Account;

class CreateUserAccount
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event)
    {
        Account::create([
            'user_id' => $event->user->id,
        ]);
    }
}
