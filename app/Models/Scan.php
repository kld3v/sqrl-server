<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $table = 'Scans';
    protected $fillable = ['url_id', 'trust_score', 'user_id', 'latitude', 'longitude'];

    public function url()
    {
        return $this->belongsTo(URL::class, 'url_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
