<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AzureSpeechController extends Controller
{
    public function assess(Request $request)
    {
        $speechKey = config('services.azure.speech_key') ?? env('AZURE_SPEECH_KEY');
        $speechRegion = config('services.azure.speech_region') ?? env('AZURE_SPEECH_REGION');

        if (!$speechKey || !$speechRegion) {
            return response()->json(['error' => 'Missing Azure Speech credentials'], 500);
        }

        $text = $request->input('text');
        $language = $request->input('language', 'zh-CN');
        $audioFile = $request->file('speech');

        if (!$audioFile) {
            return response()->json(['error' => 'No audio file uploaded'], 400);
        }

        // Build the Azure endpoint URL
        // Format: https://<region>.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language=<language>
        $endpoint = "https://{$speechRegion}.stt.speech.microsoft.com/speech/recognition/conversation/cognitiveservices/v1?language={$language}";

        // Configuration for Pronunciation Assessment
        $pronunciationAssessmentParams = [
            'ReferenceText' => $text,
            'GradingSystem' => 'HundredMark',
            'Granularity' => 'Phoneme',
            'Dimension' => 'Comprehensive',
            'EnableMiscue' => true,
        ];

        $pronunciationAssessmentHeader = base64_encode(json_encode($pronunciationAssessmentParams));

        try {
            // Read file content
            $audioContent = file_get_contents($audioFile->getRealPath());

            // Send request to Azure
            $response = Http::withHeaders([
                'Ocp-Apim-Subscription-Key' => $speechKey,
                'Content-Type' => 'audio/wav', // We expect the frontend to send WAV
                'Pronunciation-Assessment' => $pronunciationAssessmentHeader,
                'Accept' => 'application/json',
            ])->withBody($audioContent, 'audio/wav')
              ->post($endpoint);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Azure STT Error: ' . $response->body());
                return response()->json([
                    'error' => 'Azure API Error',
                    'status' => $response->status(),
                    'details' => $response->json() ?? $response->body()
                ], $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Azure Speech Controller Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }
}
