<?php

namespace App\Listeners;

use App\Events\EmployeeInvitationAccepted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserActivityInvitation
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\EmployeeInvitationAccepted  $event
     * @return void
     */
    public function handle(EmployeeInvitationAccepted $event)
    {
        //
    }
}
