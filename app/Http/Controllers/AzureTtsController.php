<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AzureTtsController extends Controller
{
    public function synthesize(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:5000',
            'rate' => 'nullable|numeric|min:0.5|max:2.0',
            'volume' => 'nullable|numeric|min:0|max:100',
            'pitch' => 'nullable|numeric|min:0.5|max:2.0',
            'voice' => 'nullable|string',
        ]);

        $text = $validated['text'];
        $rate = $validated['rate'] ?? 1.0;
        $volume = $validated['volume'] ?? 100;
        $pitch = $validated['pitch'] ?? 1.0;
        $voice = $validated['voice'] ?? 'zh-CN-XiaoxiaoNeural';

        // 1. Generate Cache Key
        $normalizedText = $this->normalizeText($text);
        $cacheKey = $this->generateCacheKey($normalizedText, $voice, $rate, $volume, $pitch);
        $cachePath = "tts_cache/{$cacheKey}.mp3";

        // 2. Check Cache
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cachePath)) {
            $fileContent = \Illuminate\Support\Facades\Storage::disk('public')->get($cachePath);
            return response($fileContent, 200)
                ->header('Content-Type', 'audio/mpeg')
                ->header('Cache-Control', 'public, max-age=31536000') // Cache for 1 year
                ->header('X-TTS-Cache', 'HIT');
        }

        $speechKey = env('AZURE_SPEECH_KEY');
        $speechRegion = env('AZURE_SPEECH_REGION');

        if (!$speechKey || !$speechRegion) {
            return response()->json([
                'error' => 'Azure Speech credentials not configured'
            ], 500);
        }

        // Construct SSML
        $ssml = $this->buildSsml($text, $voice, $rate, $volume, $pitch);

        // Call Azure TTS API
        $endpoint = "https://{$speechRegion}.tts.speech.microsoft.com/cognitiveservices/v1";

        try {
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $speechKey,
                'Content-Type' => 'application/ssml+xml',
                'X-Microsoft-OutputFormat' => 'audio-16khz-128kbitrate-mono-mp3',
                'User-Agent' => 'HSKWarrior',
            ])->withBody($ssml, 'application/ssml+xml')
              ->post($endpoint);

            if ($response->successful()) {
                $audioContent = $response->body();

                // 3. Save to Cache
                \Illuminate\Support\Facades\Storage::disk('public')->put($cachePath, $audioContent);

                // 4. Log to Database (Mapping)
                try {
                    \App\Models\TtsCacheLog::firstOrCreate(
                        ['filename_hash' => $cacheKey],
                        [
                            'text_content' => $text,
                            'voice' => $voice,
                            'rate' => $rate,
                            'volume' => $volume,
                            'pitch' => $pitch,
                        ]
                    );
                } catch (\Exception $e) {
                    // Don't fail the request if logging fails
                    Log::error('Failed to log TTS cache: ' . $e->getMessage());
                }

                return response($audioContent, 200)
                    ->header('Content-Type', 'audio/mpeg')
                    ->header('Cache-Control', 'public, max-age=31536000')
                    ->header('X-TTS-Cache', 'MISS');
            }

            Log::error('Azure TTS API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return response()->json([
                'error' => 'Failed to synthesize speech',
                'details' => $response->body()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Azure TTS Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'An error occurred while synthesizing speech',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function normalizeText($text)
    {
        // Remove all non-letter and non-number characters (keep Chinese, English, Numbers)
        // \p{L} matches any unicode letter (including Chinese)
        // \p{N} matches any unicode number
        // /u modifier enables UTF-8 support
        return preg_replace('/[^\p{L}\p{N}]/u', '', $text);
    }

    private function generateCacheKey($text, $voice, $rate, $volume, $pitch)
    {
        // Create a unique hash based on content and parameters
        $data = "{$text}|{$voice}|{$rate}|{$volume}|{$pitch}";
        return md5($data);
    }

    private function buildSsml($text, $voice, $rate, $volume, $pitch)
    {
        // Convert rate to percentage for Azure
        // Web Speech: 0.1 = very slow, 1.0 = normal, 10 = very fast
        // Azure: -100% to +100% (or 0.5x to 2.0x)
        // We'll use percentage for more flexibility
        $ratePercentage = round(($rate - 1.0) * 100);
        $rateStr = ($ratePercentage >= 0 ? '+' : '') . $ratePercentage . '%';
        
        // For very slow rates (< 0.5), we need to use percentage
        if ($rate < 0.5) {
            // Map 0.1 -> -90%, 0.5 -> -50%
            $ratePercentage = round(($rate - 1.0) * 100);
            $rateStr = $ratePercentage . '%';
        }

        // Convert pitch more conservatively
        // Web Speech: 1.0 = normal, 1.2 = slightly higher
        // Let's use smaller adjustments for Azure
        // 1.2 -> +1st instead of +2st
        $pitchSemitones = round(($pitch - 1.0) * 5); // Reduced from *10
        $pitchStr = ($pitchSemitones >= 0 ? '+' : '') . $pitchSemitones . 'st';
        
        // If pitch is very close to 1.0, use "medium" (default)
        if (abs($pitch - 1.0) < 0.1) {
            $pitchStr = 'medium';
        }

        return <<<SSML
<speak version="1.0" xmlns="http://www.w3.org/2001/10/synthesis" xml:lang="zh-CN">
    <voice name="{$voice}">
        <prosody rate="{$rateStr}" volume="{$volume}" pitch="{$pitchStr}">
            {$text}
        </prosody>
    </voice>
</speak>
SSML;
    }
}
