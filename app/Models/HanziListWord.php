<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HanziListWord extends Model
{
    use HasFactory;

    protected $table = 'hanzi_list_words';

    public $timestamps = false;

    protected $fillable = [
        "hanzi_list_id",
        "hanzi_id",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function list()
    {
        return $this->belongsTo(HanziList::class);
    }

    public function dictionaryZhHans()
    {
        return $this->belongsTo(DictionaryZhHans::class, 'hanzi_id');
    }

    public function hanzi()
    {
        return $this->belongsTo(DictionaryZhHans::class, 'hanzi_id');
    }

    public function wordGuessesForCurrentUser()
    {
        return $this->hasMany(WordGuess::class, 'dictionary_zh_hans_id', 'hanzi_id')
            ->where('user_id', auth()->id());
    }

    public function latestWordGuess()
    {
        return $this->hasOne(WordGuess::class, 'dictionary_zh_hans_id', 'hanzi_id')
            ->where('user_id', auth()->id())
            ->latest('guessed_at');
    }
}
