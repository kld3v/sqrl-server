<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredDomain extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'domain'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function domainSimilarityFlags()
    {
        return $this->hasMany(DomainSimilarityFlag::class);
    }
}
