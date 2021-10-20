<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'first_name','last_name','member_id','current_team_id','current_role_id','home_phone','cell_phone','work_phone','require_password_change', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'name',
        'permissions',
        'roles',
        'current_role',
        'role_list',
        'role_keys'
    ];

    public function getNameAttribute() { 
        return $this->first_name . " " . $this->last_name;
    }

        /**
     * Get all of the teams the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams()
    {
        return $this->belongsToMany(Jetstream::teamModel(), Jetstream::membershipModel())
            ->withPivot('roles')
            ->withTimestamps()
            ->as('membership');
    }
    
    public function teamPermissions($team)
    {
        if(! $team) return [];

        $teamMembership = TeamMember::where('team_id', $team)
            ->where('user_id', $this->id)
            ->first();

        if (! $teamMembership) {
            return [];
        }

        $permissions = [];

        $roles = json_decode($teamMembership->roles);

        foreach($roles as $role) {
            if($role === $this->current_role_id) {
                $permissions = Jetstream::findRole($role)->permissions;
            }
        }

        return ($permissions);
    }

    public function getPermissionsAttribute() {
        return $this->teamPermissions($this->current_team_id);
    }

    public function getCurrentRoleAttribute() {
        foreach($this->roles as $role) {
            if($role->key === $this->current_role_id) {
                return $role;
            }
        }
    }

    public function getRolesAttribute() {
        $teamModel = Team::find($this->current_team_id);

        $teamMembership = $teamModel->users()->where('user_id', $this->id)->first();


        if (! $teamMembership) {
            return [];
        }

        $roleObjects = [];
        $roles = json_decode(TeamMember::where('user_id', $this->id)->where('team_id', $this->current_team_id)->first()->roles);

        foreach($roles as $role) {
            $roleObjects[] = Jetstream::findRole($role);
        }

        return $roleObjects;
    }

    public function getRoleListAttribute() {
        $roleNames = [];

        foreach($this->roles as $role) {
            $roleNames[] = $role->name;
        }

        return implode(", ", $roleNames);
    }

    public function getRoleKeysAttribute() {
        $roleNames = [];

        foreach($this->roles as $role) {
            $roleNames[] = $role->key;
        }

        return $roleNames;
    }
}
