<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class URL extends Model
{
    
    protected $fillable = ['url', 'trust_score'];

    public function urlCheckLogs()
    {
        return $this->hasMany(URLCheckLog::class, 'url_id');
    }

    public function scans()
    {
        return $this->hasMany(Scan::class, 'url_id');
    }

    public function venues()
    {
        return $this->hasMany(Venue::class, 'url_id');
    }
}
