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
    // 🔗 Relasi: Tim bisa jadi lawan di banyak Batting Order
    public function opponents()
    {
        return $this->hasMany(Batting::class, 'opponent_id');
    }
}
