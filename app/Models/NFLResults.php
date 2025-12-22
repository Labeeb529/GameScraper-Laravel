<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFLResults extends Model
{
    protected $table = 'nfl_results';

    protected $fillable = [
        'game_date',
        'game_time',
        'team_left',
        'team_right',
        'score_left',    
        'score_right',    
        'winning_spread',    
    ];
    public $timestamps = false;
}
