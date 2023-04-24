<?php

namespace App\Listeners;

use App\Events\InvitationSent;
use App\Mail\InvitationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class InvitationEmail
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
     * @param  \App\Events\InvitationSent  $event
     * @return void
     */
    public function handle(InvitationSent $event)
    {
        $invitation = $event->invitation;
        Mail::to($invitation->email)->queue(new InvitationMail($invitation));
    }
}
