<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{

    protected $fillable = [
        'company',
        'chain',
        'url_id',
        'tel',
        'address',
        'postcode',
        'google_maps',
        'area',
        'midpoint',
        'status',
        'complete'
    ];

    public $timestamps = true;

    public function url()
    {
        return $this->belongsTo(URL::class, 'url_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function monitoredDomains()
    {
        return $this->hasMany(MonitoredDomain::class);
    }

    public function monitoredRedirectPaths()
    {
        return $this->hasMany(MonitoredRedirectPath::class);
    }
}
