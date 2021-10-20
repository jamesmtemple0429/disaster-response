<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use App\Models\TeamMember;

class TeamMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::firstOrCreate([
            'first_name'                => $request->firstName,
            'last_name'                 => $request->lastName,
            'member_id'                 => $request->memberID,
            'email'                     => $request->email
        ], [
            'require_password_change'   => true,
            'password'                  => bcrypt(strtolower($request->firstName[0] . $request->lastName)),
            'current_team_id'           => $request->id,
            'current_role_id'           => $request->roles[0]
        ]);

        $team = Team::find($request->id);

        $teamMember = TeamMember::where('team_id',$team->id)
            ->where('user_id',$user->id)
            ->first();


        if($teamMember) {
            $databaseRoles = json_decode($teamMember->roles);

            $teamMember->update([
                'roles'         => array_unique(array_merge($databaseRoles, $request->roles))
            ]);
        } else {
            TeamMember::create([
                'team_id'       => $team->id,
                'user_id'       => $user->id,
                'roles'         => json_encode($request->roles)
            ]);
        }

        return redirect()->route('teams.show', $team);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $team, $user)
    {
        $membership = TeamMember::where('team_id', $team)
            ->where('user_id', $user)
            ->first();
        
        $membership->update(['roles' => json_encode($request->roles)]);
        
        return redirect()->route('teams.show', $team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function destroy($id)
    {
        //
    }
}
