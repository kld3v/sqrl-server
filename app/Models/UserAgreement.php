<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAgreement extends Model
{
    protected $fillable = ['device_uuid', 'document_version_id', 'timestamp'];

    public function document_version()
    {
        return $this->belongsTo(DocumentVersion::class, 'document_version_id');
    }
}