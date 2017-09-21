<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['option_a'];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function answer()
    {
        return $this->hasMany(Answer::class);
    }
}
