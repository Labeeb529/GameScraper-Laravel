<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NCAAFResults extends Model
{
    protected $table = "ncaaf_results";

    protected $fillable = [
        'game_date',
        'game_time',
        'team_left',
        'team_right',
        'score_left',
        'score_right',
        'winning_spread',
        'data_index_id',
    ];

    public $timestamps = false;

    public function dataIndex()
    {
        return $this->belongsTo(DataIndex::class, 'data_index_id');
    }


}
