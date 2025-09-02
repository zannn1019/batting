<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batting extends Model
{
    protected $fillable = [
        'team_id',
        'opponent_id',
        'game_date',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $table = 'batting';

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function opponent()
    {
        return $this->belongsTo(Team::class, 'opponent_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function battingOrders()
    {
        return $this->hasMany(BattingOrder::class, 'batting_id');
    }
}
