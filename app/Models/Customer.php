<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    use Notifiable;
    protected $fillable = ['username','password','tel'];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
