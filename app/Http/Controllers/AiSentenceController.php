<?php

namespace App\Http\Controllers;

use App\Models\AiSentence;
use App\Services\AiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiSentenceController extends Controller
{
    protected $aiChatService;

    public function __construct(AiChatService $aiChatService)
    {
        $this->aiChatService = $aiChatService;
    }

    public function generate(Request $request)
    {
        $request->validate([
            'word' => 'required|string',
            'level' => 'nullable|string'
        ]);

        $word = $request->input('word');
        $level = $request->input('level', 'HSK 1');

        $forceRefresh = $request->input('force_refresh', false);
        
        // 1. Check if we already have sentences for this word
        // We can return existing ones to save API costs and time
        // But the user might want *new* examples.
        // For now, let's always fetch from DB first, if we have enough.
        // If the user wants "more", we might need a flag.
        // The requirement says "check hash to not save duplicates".
        
        // Let's try to find existing sentences first
        $existingSentences = AiSentence::where('word', $word)->get();
        
        if (!$forceRefresh && $existingSentences->count() >= 5) {
             return response()->json([
                'sentences' => $existingSentences
            ]);
        }

        // 2. Generate new sentences
        $data = $this->aiChatService->generateSentences($word, $level);

        if (!$data || !isset($data['sentences'])) {
            return response()->json(['error' => 'Failed to generate sentences'], 500);
        }

        $newSentences = [];

        foreach ($data['sentences'] as $item) {
            // Create hash to prevent duplicates
            // Hash based on sentence content
            $hash = md5($item['sentence'] . '|' . $item['translation']);

            // Check if this specific sentence already exists (globally or for this word)
            // The hash is unique in the table, so we check existence by hash
            $exists = AiSentence::where('hash', $hash)->exists();

            if (!$exists) {
                $sentence = AiSentence::create([
                    'word' => $word,
                    'sentence' => $item['sentence'],
                    'pinyin' => $item['pinyin'],
                    'translation' => $item['translation'],
                    'hash' => $hash,
                ]);
                $newSentences[] = $sentence;
            } else {
                // If it exists, we might want to return it anyway if it matches the word
                $existing = AiSentence::where('hash', $hash)->first();
                if ($existing->word === $word) {
                    $newSentences[] = $existing;
                }
            }
        }

        // Merge with previously existing ones and return
        $allSentences = $existingSentences->merge($newSentences);

        return response()->json([
            'sentences' => $allSentences
        ]);
    }
}
