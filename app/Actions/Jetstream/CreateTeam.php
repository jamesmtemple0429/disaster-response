<?php

namespace App\Actions\Jetstream;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Events\AddingTeam;
use Laravel\Jetstream\Jetstream;
use App\Models\Team;

class CreateTeam implements CreatesTeams
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return mixed
     */
    public function create($user, array $input)
    {
        Gate::forUser($user)->authorize('create', Jetstream::newTeamModel());

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required','max:255']
        ])->validateWithBag('createTeam');

        AddingTeam::dispatch($user);

        $team = Team::create([
            'name'          => $input['name'],
            'type'          => $input['type'],
            'national_id'   => $input['nationalID'],
            'division_id'   => ($input['divisionID'] === 0) ? null : $input['divisionID']
        ]);

        $team->users()->attach($user, ['roles' => json_encode([$user->current_role_id])]);

        return $team;
    }
}
