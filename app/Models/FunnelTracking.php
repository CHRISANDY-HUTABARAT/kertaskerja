<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FunnelTracking extends Model
{
    use HasFactory;

    protected $table = 'funnel_tracking';

    protected $fillable = [
        'data_type',
        'data_id',
        'ngtma_id',
        // F0
        'f0_inisiasi_solusi',
        // F1
        'f1_tech_budget',
        // F2
        'f2_p0_p1',
        'f2_p2',
        'f2_p3',
        'f2_p4',
        'f2_offering',
        'f2_p5',
        'f2_proposal',
        // F3
        'f3_p6',
        'f3_p7',
        'f3_submit',
        // F4
        'f4_negosiasi',
        // F5
        'f5_sk_mitra',
        'f5_ttd_kontrak',
        'f5_p8',
        // DELIVERY
        'delivery_kontrak',
        'delivery_baut_bast',
        'delivery_baso',
        'delivery_billing_complete',
        'delivery_nilai_billcomp',
        'cancel',
    ];

    protected $casts = [
        // F0
        'f0_inisiasi_solusi' => 'boolean',
        // F1
        'f1_tech_budget' => 'boolean',
        // F2
        'f2_p0_p1' => 'boolean',
        'f2_p2' => 'boolean',
        'f2_p3' => 'boolean',
        'f2_p4' => 'boolean',
        'f2_offering' => 'boolean',
        'f2_p5' => 'boolean',
        'f2_proposal' => 'boolean',
        // F3
        'f3_p6' => 'boolean',
        'f3_p7' => 'boolean',
        'f3_submit' => 'boolean',
        // F4
        'f4_negosiasi' => 'boolean',
        // F5
        'f5_sk_mitra' => 'boolean',
        'f5_ttd_kontrak' => 'boolean',
        'f5_p8' => 'boolean',
        // DELIVERY
        'delivery_kontrak' => 'boolean',
        'delivery_baut_bast' => 'boolean',
        'delivery_baso' => 'boolean',
        'delivery_billing_complete' => 'boolean',
        'delivery_nilai_billcomp' => 'decimal:2',
        'cancel' => 'boolean',
    ];

    /**
     * Get all progress records for this task
     */
    public function progress()
    {
        return $this->hasMany(TaskProgress::class, 'task_id');
    }

    public function data()
    {
        return $this->belongsTo(ScallingData::class, 'data_id');
    }

    public function ngtma()
    {
        return $this->belongsTo(Ngtma::class, 'ngtma_id');
    }

    /**
     * Get today's progress for current user
     */
    public function todayProgress()
    {
        return $this->hasOne(TaskProgress::class, 'task_id')
            ->whereDate('tanggal', today())
            ->where('user_id', auth()->id());
    }

    /**
     * Scope to eager load today's progress for a specific user
     */
    public function scopeWithTodayProgress($query, $userId)
    {
        return $query->with(['todayProgress' => function($q) use ($userId) {
            $q->where('user_id', $userId)->whereDate('tanggal', today());
        }]);
    }
}
