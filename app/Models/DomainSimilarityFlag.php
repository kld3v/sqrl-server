<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainSimilarityFlag extends Model
{
    use HasFactory;

    protected $fillable = ['monitored_domain_id', 'similar_url', 'trust_score'];

    public function monitoredDomain()
    {
        return $this->belongsTo(MonitoredDomain::class);
    }
}
