<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_team',
        'full_name',
        'birth_date',
        'position',
        'back_number',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'id_team');
    }

    public function battingOrders()
    {
        return $this->hasMany(BattingOrder::class, 'id_player');
    }
}
