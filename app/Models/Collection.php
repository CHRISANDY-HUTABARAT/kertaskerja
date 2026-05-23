<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'user_id', 'type', 'status', 'segment', 'kondisi',
    'periode', 'plan', 'ol_fm', 'commitment', 'real_ratio', 'is_latest',
    'real_updated_at',
    'file_path',
    'file_name',
    'submit_token',
];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
    'plan'            => 'float',
    'commitment'      => 'float',
    'real_ratio'      => 'float',
    'created_at'      => 'datetime',
    'updated_at'      => 'datetime',
    'real_updated_at' => 'datetime',
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
