<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedRegion extends Model
{
    use HasFactory;

    protected $fillable = ['dispatch_center_id','region_id'];

    public function dispatch_center() {
        return $this->hasOne(Team::class, "id", "dispatch_center_id");
    }

    public function region() {
        return $this->hasOne(Team::class, "id","region_id");
    }
}
