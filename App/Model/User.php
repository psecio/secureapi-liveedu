<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    const STATUS_ACTIVE = 'active';
    const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'username', 'password', 'email', 'name', 'status', 'password_reset_date'
    ];
}
