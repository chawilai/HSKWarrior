<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TtsCacheLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'text_content',
        'filename_hash',
        'voice',
        'rate',
        'volume',
        'pitch',
    ];
}
