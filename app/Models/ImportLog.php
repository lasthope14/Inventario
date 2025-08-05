<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'records_imported',
        'details',
        'imported_ids',
        'files_processed',
        'status',
        'reverted'
    ];

    protected $casts = [
        'details' => 'array',
        'imported_ids' => 'array',
        'files_processed' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Usuario no disponible'
        ]);
    }
}