<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSentence extends Model
{
    protected $fillable = [
        'word',
        'sentence',
        'pinyin',
        'translation',
        'hash',
    ];
}
