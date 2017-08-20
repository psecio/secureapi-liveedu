<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ApiSession extends Model
{
    const TIMEOUT = '+1 hour';

    protected $table = 'api_session';
    protected $fillable = [
        'key_id', 'session_id', 'expiration', 'user_id'
    ];

    public function user()
    {
        // This is how an Eloquent relationship is created
        return $this->hasOne('\App\Model\User', 'id', 'user_id');
    }
}
