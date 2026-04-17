<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gap extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'segment', 'periode', 'value',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
