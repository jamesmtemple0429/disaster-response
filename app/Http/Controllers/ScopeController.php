<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TeamMember;

class ScopeController extends Controller
{
    public function changeRole(Request $request) {
        \Auth::user()->update(['current_role_id' => $request->role['key']]);

        return redirect()->route('dashboard');
    }

    public function changeTeam(Request $request) {
        $teamMember = TeamMember::where('team_id',$request->team_id)->first();

        $defaultRole = json_decode($teamMember->roles)[0];

        \Auth::user()->update([
            'current_role_id' => $defaultRole,
            'current_team_id' => $request->team_id
        ]);

        return redirect()->route('dashboard');
    }
}
