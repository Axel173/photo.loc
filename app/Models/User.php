<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_roles', 'user_id', 'role_id');

    }

    public function isEmployee()
    {
        $roles = $this->roles()->tpArray();
        return !empty($roles);
    }

    public function hasRole($check)
    {
        return in_array($check, array_pluck($this->roles->toArray(), 'name'));
    }

    public function getIdInArray($array, $term)
    {
        foreach ($array as $key => $value)
        {
            if($value == $term)
            {
                return $key + 1;
            }
        }

        return false;
    }

    public function makeEmployee($title)
    {
        $assigned_roles = array();
        $roles = array_pluck(Role::all()->toArray(), 'name');
        switch ($title)
        {
            case 'admin':
                $assigned_roles[] = $this->getIdInArray($roles, 'admin');
            case 'user':
                $assigned_roles[] = $this->getIdInArray($roles, 'user');
                break;
            default:
                $assigned_roles[] = false;
        }

        $this->roles()->attach($assigned_roles);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
