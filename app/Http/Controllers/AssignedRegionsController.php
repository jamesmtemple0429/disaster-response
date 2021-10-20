<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssignedRegion;

class AssignedRegionsController extends Controller
{
    public function assign(Request $request) {
        AssignedRegion::create($request->only([
            'dispatch_center_id',
            'region_id'
        ]));

        return redirect()->route('teams.show', $request->dispatch_center_id);
    }

    public function unassign(Request $request) {
        AssignedRegion::where('dispatch_center_id', $request->dispatch_center_id)
            ->where('region_id', $request->region_id)
            ->delete();

        return redirect()->route('teams.show', $request->dispatch_center_id);
    }
}
