<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NCAABResults extends Model
{
    protected $table = "ncaab_results";

    protected $fillable = [
        'game_date',
        'game_time',
        'team_left',
        'team_right',
        'spread_left',
        'spread_right',
        'perc_bets_left',
        'perc_bets_right',
        'perc_money_left',
        'perc_money_right',
    ];

    public $timestamps = false;

}
