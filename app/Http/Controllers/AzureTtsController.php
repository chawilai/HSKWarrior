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
                return response($response->body(), 200)
                    ->header('Content-Type', 'audio/mpeg')
                    ->header('Cache-Control', 'public, max-age=3600');
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
