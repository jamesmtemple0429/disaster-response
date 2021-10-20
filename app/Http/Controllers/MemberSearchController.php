<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class MemberSearchController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $foundUser = User::where('member_id', $request->member_id)->first();
        return ($foundUser) ? $foundUser : "N/A";
    }
}
