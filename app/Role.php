<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    protected $fillable = [
        'name'
    ];

    public function getUsers()
    {
        return $this->hasMany('App\User', 'role_id');
    }


    public function permissions()
    {
        return $this->belongsToMany('App\Permission');
    }

    public function has_perm(Array $permissions_array, $role_id = false)
    {
        if (!$role_id)
            $role_id = Auth::user()->role_id;
        if ($role_id == 1)
            return true;
        $available = DB::table('role_permissions')
            ->select('id')
            ->where('role_id', '=', $role_id)
            ->whereIn('permission_id', $permissions_array)
            ->get();

        return count($available) > 0;
    }

    public function has_perm_users(Array $permissions_array)
    {
        $users = DB::table('role_permissions')
            ->join('roles', 'roles.id', '=', 'role_permissions.role_id')
            ->join('users', 'users.role_id', '=', 'roles.id')
            ->select('users.*')
            ->whereIn('role_permissions.permission_id', $permissions_array)
            ->get();
        return $users;
    }
}
