<?php

namespace App\Listeners;

use App\Events\EmployeeInvitationAccepted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LogUserInvitationConfirmed
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
        $date = $event->date;
        $invitation = $event->invitation;
        if ($invitation->status === 'confirmed') {
            return;
        }
        $invitation->status = 'confirmed';
        $invitation->save();
        Log::channel('activity_log')->info($date->format('d-m-Y - H:i') . ' /  "' . $invitation->name . '" à valider l’invitation "');
    }
}
