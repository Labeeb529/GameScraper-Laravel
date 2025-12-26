<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NFLBets extends Model
{
    protected $table = 'nfl_bets';

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
        'data_index_id',
    ];
    public $timestamps = false;

    public function dataIndex()
    {
        return $this->belongsTo(DataIndex::class, 'data_index_id');
    }


}
