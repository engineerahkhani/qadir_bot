<?php

namespace App;

use App\Code;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'email',
        'chat_id',
        'name'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
