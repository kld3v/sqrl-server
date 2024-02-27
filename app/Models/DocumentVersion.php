<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $fillable = ['document_name', 'version', 'document_url', 'is_active'];

    
    public function user_agreement()
    {
        return $this->hasMany(UserAgreement::class, 'document_version_id');
    }
}
