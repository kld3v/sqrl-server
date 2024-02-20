<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BugResponse extends Model
{
    protected $fillable = ['device_uuid','bug_description','status','report_date','resolution_date'];
}
