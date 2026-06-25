<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hsi extends Model
{
    protected $fillable = [
    'user_id', 'status', 'type',
    'periode', 'segment', 'commitment', 'real_ratio',
    'real_updated_at',
];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    'created_at'      => 'datetime',
    'updated_at'      => 'datetime',
    'real_updated_at' => 'datetime',
    'commitment'      => 'decimal:2',
    'real_ratio'      => 'decimal:2',
];

protected static function booted(): void
{
    static::updating(function ($model) {
        if ($model->isDirty('real_ratio')) {
            $model->real_updated_at = now();
        }
    });
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
