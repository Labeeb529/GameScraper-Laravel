<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataIndex extends Model
{
    protected $table = 'data_index';

    protected $fillable = [
        'scraper_name',
        'scraped_at',
        'rows_found',
        'rows_merged',
        'date',
        'week',
        'year',
    ];

    public function nflBets()
    {
        return $this->hasMany(NFLBets::class);
    }

    public function nflResults()
    {
        return $this->hasMany(NFLResults::class);
    }

    public function ncaafBets()
    {
        return $this->hasMany(NCAAFBets::class);
    }

    public function ncaafResults()
    {
        return $this->hasMany(NCAAFResults::class);
    }

    public function ncaabBets()
    {
        return $this->hasMany(NCAABBets::class);
    }

    public function ncaabResults()
    {
        return $this->hasMany(NCAABResults::class);
    }
}
