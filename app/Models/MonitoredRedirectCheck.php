<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoredRedirectCheck extends Model
{
    use HasFactory;

    protected $fillable = ['monitored_redirect_path_id', 'final_url', 'is_safe'];

    public function monitoredRedirectPath()
    {
        return $this->belongsTo(MonitoredRedirectPath::class);
    }

}
