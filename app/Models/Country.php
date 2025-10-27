<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name',
        'capital',
        'region',
        'population',
        'currency_code',
        'exchange_rate',
        'estimated_gdp',
        'flag_url',
        'last_refreshed_at',
    ];


    protected $casts = [
        'last_refreshed_at' => 'datetime',
    ];


    // protected $appends = [
    //     'gdp_per_capita',
    // ];

    // public function getGdpPerCapitaAttribute(): float
    // {
    //     return $this->estimated_gdp / $this->population;
    // }
}
