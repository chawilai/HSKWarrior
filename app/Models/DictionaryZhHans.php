<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DictionaryZhHans extends Model
{
    use HasFactory;

    protected $table = 'dictionary_zh_hans';

    protected $fillable = [
        'character',
        'set',
        'definition',
        'pinyin',
        'radical',
        'decomposition',
        'acjk',
    ];

    protected $casts = [
        'set' => 'array',
        'pinyin' => 'array',
    ];
}
