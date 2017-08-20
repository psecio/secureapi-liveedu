<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $table = 'api_key';

    /**
     * Relate the api key to the User
     */
    public function user()
    {
        return $this->hasOne('\App\Model\User', 'id', 'user_id');
    }

    public function session()
    {
        return $this->hasOne('\App\Model\ApiSession', 'key_id', 'id');
    }
}
