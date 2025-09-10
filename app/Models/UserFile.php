<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class UserFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'path'
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
