<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DictionaryZhHans;
use App\Models\User;

class WordGuess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dictionary_zh_hans_id',
        'is_correct',
        'guessed_at',
    ];

    public function word()
    {
        return $this->belongsTo(DictionaryZhHans::class, 'dictionary_zh_hans_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
