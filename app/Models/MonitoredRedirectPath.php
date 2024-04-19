<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredRedirectPath extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'initial_url', 'expected_url'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function urlDestinationChecks()
    {
        return $this->hasMany(UrlDestinationCheck::class);
    }
}
