<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ngtma extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'project',
        'id_lop',
        'cc',
        'nipnas',
        'am',
        'mitra',
        'plan_bulan_billcomp_2025',
        'est_nilai_bc',
        'is_manual',
        'imports_log_id',
    ];

    protected $casts = [
        'est_nilai_bc' => 'decimal:2',
        'plan_bulan_billcomp_2025' => 'integer',
        'no' => 'integer',
    ];

    public function scallingImport()
    {
        // column name from migration: imports_log_id
        return $this->belongsTo(ScallingImport::class, 'imports_log_id');
    }

    /**
     * Relation to funnel tracking master record for this row.
     * data_id in funnel_tracking refers to this scalling_data id.
     */
    public function funnel()
    {
        return $this->hasOne(\App\Models\FunnelTracking::class, 'data_id');
    }
}
