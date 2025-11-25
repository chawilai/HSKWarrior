<?php

namespace App\Http\Controllers;

use App\Models\ChineseWord;
use Illuminate\Http\Request;

class ReadingGameController extends Controller
{
    public function getContent(Request $request)
    {
        $type = $request->input('type', 'word'); // 'word' or 'sentence'
        $level = $request->input('level', 1);
        $category = $request->input('category'); // POS tag, e.g., 'v.', 'n.'

        $hskTag = "HSK " . $level;

        $query = ChineseWord::whereHas('tags', function ($q) use ($hskTag) {
            $q->where('name', $hskTag);
        });

        if ($type === 'word') {
            if ($category) {
                // Handle special categories if needed, or direct mapping
                // For now, assume category is the POS tag
                $query->where('part_of_speech', $category);
            }

            $items = $query->inRandomOrder()->take(20)->get(['id', 'word as text', 'pinyin']);
        } else {
            // For sentences, we use the examples from the words
            // Ensure example is not null/empty
            $items = $query->whereNotNull('example')
                ->where('example', '!=', '')
                ->inRandomOrder()
                ->take(10)
                ->get(['id', 'example as text', 'example_pinyin as pinyin']);
        }

        return response()->json($items);
    }
}
