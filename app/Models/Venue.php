<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';

    protected $fillable = [
        'company',
        'chain',
        'url_id',
        'tel',
        'address',
        'postcode',
        'google_maps',
        'area',
        'status'
    ];

    public $timestamps = true;

    public function url()
    {
        return $this->belongsTo(URL::class, 'url_id');
    }
}
