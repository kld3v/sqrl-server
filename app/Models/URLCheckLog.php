<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// TODO consider naming to UrlCheck
class URLCheckLog extends Model
{
    protected $table = 'url_checks_log';
    protected $fillable = ['url_id', 'test_result', 'trust_score'];

    public function url()
    {
        return $this->belongsTo(URL::class, 'url_id');
    }
}
