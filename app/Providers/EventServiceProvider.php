<?php

namespace App\Providers;

use App\Events\EmployeeConfirmed;
use App\Events\EmployeeInvitationAccepted;
use App\Events\EmployeeProfileConfirmed;
use App\Events\InvitationClicked;
use App\Events\TrackProcessed;
use App\Listeners\LogUserInvitationConfirmed;
use App\Listeners\UpdateInvitationStatusListener;
use App\Listeners\UserActivity;
use App\Listeners\UserActivityInvitation;
use App\Listeners\UserProfileConfirm;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // InvitationClicked::class => [
        //     UpdateInvitationStatusListener::class,
        // ],
        TrackProcessed::class => [
            UserActivity::class,
        ],
        EmployeeInvitationAccepted::class => [
            LogUserInvitationConfirmed::class,
        ],
        EmployeeConfirmed::class => [
            UserProfileConfirm::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
