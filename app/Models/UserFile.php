<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class UserFile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'original_name', 'path', 'output_path', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}