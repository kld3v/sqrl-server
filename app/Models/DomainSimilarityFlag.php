<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainSimilarityFlag extends Model
{
    use HasFactory;

    protected $fillable = ['monitored_domain_id', 'similar_url_id', 'trust_score'];

    public function monitoredDomain()
    {
        return $this->belongsTo(MonitoredDomain::class);
    }

    public function similarUrl()
    {
        return $this->belongsTo(URL::class, 'similar_url_id');
    }
}
