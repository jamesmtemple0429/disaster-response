<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        // 4 - National
        Jetstream::role('admin', 'Administrator', [
            '*'
        ])->description('Administrator users can perform any action.');

        // 3 - Divisional
        Jetstream::role('div-admin', 'Division Administrator', [
            '*'
        ])->description('Administrator users can perform any action.');

        Jetstream::role('div-leader', 'Division Leadership', [
            '*'
        ])->description('Administrator users can perform any action.');

        // Applies to both 1 - Regional & 2 - Dispatch
        Jetstream::role('region-admin', 'Regional Administrator', [
            'teams.update.own',
            'teams.members.manage'
        ])->description('Administrator users can perform any action.');

        Jetstream::role('region-leader', 'Regional Leadership', [
            '*'
        ])->description('Administrator users can perform any action.');

        Jetstream::role('editor', 'Schedule Editor', [
            '*'
        ])->description('Administrator users can perform any action.');


        // 2 - Dispatch
        Jetstream::role('dispatch-sv', 'Dispatch Supervisor', [
            '*'
        ])->description('Administrator users can perform any action.');
        Jetstream::role('dispatch', 'Dispatcher', [
            '*'
        ])->description('Administrator users can perform any action.');

        // 1 - Regional
        Jetstream::role('dro', 'Call Center Liaison', [
            '*'
        ])->description('Administrator users can perform any action.');
        Jetstream::role('responder-sv', 'Responder Supervisor', [
            '*'
        ])->description('Administrator users can perform any action.');
        Jetstream::role('responder', 'Responder', [
            '*'
        ])->description('Administrator users can perform any action.');
    }
}
