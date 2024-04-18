<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredRedirectPath extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'initial_url_id', 'expected_url_id'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function initialUrl()
    {
        return $this->belongsTo(URL::class, 'initial_url_id');
    }

    public function expectedUrl()
    {
        return $this->belongsTo(URL::class, 'expected_url_id');
    }

    public function urlDestinationChecks()
    {
        return $this->hasMany(UrlDestinationCheck::class);
    }
}
