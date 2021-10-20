<?php
    Route::get('teams','TeamsController@index');
    Route::get('assigned-regions/{team}','TeamsController@assignedRegions');