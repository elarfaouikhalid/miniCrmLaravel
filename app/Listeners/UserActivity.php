<?php

namespace App\Listeners;

use App\Events\TrackProcessed;
use App\Mail\InvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserActivity
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
     * @param  \App\Events\TrackProcessed  $event
     * @return void
     */
    public function handle(TrackProcessed $event)
    {
        $invitation = $event->invitation;
        $date = $event->date;
        $companyName = $event->companyName;
        $adminName = $event->adminName;
        // send email
        Mail::to($invitation->email)->queue(new InvitationMail($invitation));
        // stock behavior in storage/logs/activity.log
        Log::channel('activity_log')->info($date->format('d-m-Y - H:i') . ' / Admin "' . $adminName . '" a invite l\'employé "' . $invitation->name . '" à joindre la société "' . $companyName . '"');
    }
}
