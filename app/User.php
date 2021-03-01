<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id','first_name','surname','gender','consent','msisdn', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    // public function partner()
    // {
    //     return $this->hasMany('App\Partner','partner_users');
    // }

    public function hcw()
    {
        return $this->hasOne('App\HealthCareWorker');
    }


    public function toArray() {
        $data = parent::toArray();
        $data['cadre'] = optional(optional($this->hcw)->cadre)->name;
        $data['dob'] = optional($this->hcw)->dob;
        $data['partner'] = optional($this->partner);
        $data['county'] = optional(optional(optional($this->hcw)->facility)->county)->name;
        $data['sub_county'] = optional(optional(optional($this->hcw)->facility)->sub_county)->name;
        $data['role'] = $this->role;
        $data['hcw'] = $this->hcw;

        return $data;
    }
}
