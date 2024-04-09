<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateUserAccount
{
    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event)
    {
        Account::create([
            'user_id' => $event->user->id
        ]);
    }
}
