<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    public function get_permission()
    {
        return $this->belongsTo('App\Permission', 'permission_id');
    }

    public function get_role()
    {
        return $this->belongsTo('App\Role', 'role_id');
    }
}
