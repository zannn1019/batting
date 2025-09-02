<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BattingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'batting_id',
        'id_player',
        'batting_position',
    ];

    protected $table = 'batting_order';

    public function batting()
    {
        return $this->belongsTo(Batting::class, 'batting_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'id_player');
    }
}
