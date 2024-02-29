<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    
    protected $fillable = ['url_id', 'trust_score', 'device_uuid', 'latitude', 'longitude', 'test_version'];

    public function url()
    {
        return $this->belongsTo(URL::class, 'url_id');
    }

}
