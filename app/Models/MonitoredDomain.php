<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredDomain extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'url_id'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function url()
    {
        return $this->belongsTo(URL::class, 'url_id');
    }

    public function domainSimilarityFlags()
    {
        return $this->hasMany(DomainSimilarityFlag::class);
    }
}
