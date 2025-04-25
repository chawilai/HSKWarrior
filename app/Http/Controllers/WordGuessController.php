<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WordGuess;
use Illuminate\Support\Facades\Auth;

class WordGuessController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'dictionary_zh_hans_id' => 'required|exists:dictionary_zh_hans,id',
            'is_correct' => 'required|boolean',
        ]);

        WordGuess::create([
            'user_id' => Auth::id(),
            'dictionary_zh_hans_id' => $request->input('dictionary_zh_hans_id'),
            'is_correct' => $request->input('is_correct'),
            'guessed_at' => now(),
        ]);

        return back()->with('success', 'Guess saved!');
    }
}
