<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HanziList extends Model
{
    use HasFactory;

    protected $table = 'hanzi_lists';

    protected $fillable = [
        "user_id",
        "list_reference",
        "list_name",
        "box_number",
    ];

    public function word()
    {
        return $this->hasMany(HanziListWord::class);
    }

    public function hanziLists()
    {
        return $this->belongsToMany(HanziList::class, 'hanzi_list_words', 'hanzi_id', 'hanzi_list_id');
    }
}
