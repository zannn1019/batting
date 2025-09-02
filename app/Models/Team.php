<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'id_manager',
        'team_name',
        'head_coach',
        'coach',
    ];

    protected $with = ['players'];

    public function manager()
    {
        return $this->belongsTo(User::class, 'id_manager');
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'id_team');
    }

    public function battings()
    {
        return $this->hasMany(Batting::class, 'team_id');
    }
    public function opponents()
    {
        return $this->hasMany(Batting::class, 'opponent_id');
    }
}
