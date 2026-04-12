<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScallingImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_filename',
        'status',        
        'periode',        
        'type',
        'segment',
        'total_rows_imported',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function scallingData()
    {
        // table column is "imports_log_id" (plural) according to the migration
        return $this->hasMany(ScallingData::class, 'imports_log_id');
    }

    public function ngtma()
    {
        // table column is "imports_log_id" (plural) according to the migration
        return $this->hasMany(Ngtma::class, 'imports_log_id');
    }
    
    public function koreksi()
    {
        // table column is "imports_log_id" (plural) according to the migration
        return $this->hasMany(Koreksi::class, 'imports_log_id');
    }

    /**
     * Alias for `scallingData` so templates can use `$import->data` like before.
     */
    public function data()
    {
        return $this->scallingData();
    }
}