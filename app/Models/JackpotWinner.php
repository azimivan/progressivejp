<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JackpotWinner extends Model
{
    use HasFactory;
    protected $fillable = [
        'jackpot_id',
        'game_table_id',
        'table_name',
        'sensor_number',
        'win_amount',
        'is_settled',
        'deduction_source',
        'hand_id', 
        'current_jackpot_amount', 
        'settled_by', // New field
    ];
    
    public function settledBy()
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function jackpot()
    {
        return $this->belongsTo(Jackpot::class);
    }

    public function gameTable()
    {
        return $this->belongsTo(GameTable::class);
    }

    public function hand()
    {
        return $this->belongsTo(Hand::class);
    }
}
