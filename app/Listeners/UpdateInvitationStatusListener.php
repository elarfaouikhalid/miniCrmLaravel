<?php

namespace App\Listeners;

use App\Events\EmployeeProfileConfirmed;
use App\Models\Invitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateInvitationStatusListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(EmployeeProfileConfirmed $event)
    {
        // $invitation = $event->employeeacount;
        // $date = $event->date;
        // $EmployeeStatus = Invitation::where('email', $invitation->email)->first();

        // if ($EmployeeStatus) {
        //     $EmployeeStatus->status = 'validated';
        //     $EmployeeStatus->save();
        // }
        // Log::channel('activity_log')->info($date->format('d-m-Y - H:i') . ' / "' . $invitation->name . '" à confirmer son profile "');
    }
}
