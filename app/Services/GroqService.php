<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService
{
    /**
     * Review an essay answer using Groq API.
     * If no API key is set, it falls back to a smart, realistic local mock grading system.
     *
     * @param string $question
     * @param string $answer
     * @param string|null $idealAnswer
     * @param string|null $keywords
     * @return array
     */
    public function reviewEssay(string $question, string $answer, ?string $idealAnswer = null, ?string $keywords = null): array
    {
        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model', 'llama-3.3-70b-versatile');

        // IF NO API KEY IS CONFIGURED: Run smart local mock grading system for free testing!
        if (empty($apiKey)) {
            Log::info('No Groq API Key found. Using free mock grading system for testing.');
            return $this->getFallbackResponse($answer, $idealAnswer, $keywords);
        }

        try {
            $idealContext = !empty($idealAnswer) ? " Kunci Jawaban Acuan / Rubrik Penilaian: {$idealAnswer}." : "";
            $keywordContext = !empty($keywords) ? " Kata Kunci Wajib yang Harus Dinilai Keberadaannya dalam Jawaban Peserta: {$keywords}. Periksa apakah kata kunci tersebut digunakan secara tepat dan relevan." : "";
            $prompt = "Kamu adalah penilai profesional onboarding karyawan. Soal: {$question}.{$idealContext}{$keywordContext} Jawaban Peserta: {$answer}. Berikan skor 0-100, feedback singkat, dan saran perbaikan dalam Bahasa Indonesia. Kembalikan data dalam format JSON dengan key: 'score' (integer), 'feedback' (string), dan 'suggestion' (string).";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])
            ->timeout(15)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $contentString = $data['choices'][0]['message']['content'] ?? '{}';
                $content = json_decode($contentString, true);

                if (isset($content['score'])) {
                    return [
                        'score' => (int) $content['score'],
                        'feedback' => $content['feedback'] ?? '',
                        'suggestion' => $content['suggestion'] ?? ''
                    ];
                }
            }

            Log::error('Groq API call failed or returned invalid structure: ' . $response->body());
            return $this->getFallbackResponse($answer, $idealAnswer, $keywords);

        } catch (\Exception $e) {
            Log::error('Groq Service Exception: ' . $e->getMessage());
            return $this->getFallbackResponse($answer, $idealAnswer, $keywords);
        }
    }

    /**
     * Safe fallback response with keyword awareness.
     */
    private function getFallbackResponse(string $answer, ?string $idealAnswer = null, ?string $keywords = null): array
    {
        $cleanAnswer = trim($answer);
        $length = strlen($cleanAnswer);

        if ($length < 15) {
            return [
                'score' => rand(20, 35),
                'feedback' => 'Jawaban terlalu singkat dan kurang menjelaskan konsep yang ditanyakan.',
                'suggestion' => 'Berikan penjelasan yang lebih rinci dan berikan contoh konkret untuk memperkuat argumen Anda.'
            ];
        }

        $defaultKeywords = ['orm', 'eloquent', 'query builder', 'controller', 'model', 'view', 'database', 'mvc', 'service provider', 'boot', 'register', 'class', 'object', 'transaction', 'rollback', 'middleware', 'injection', 'container', 'queue', 'job', 'auth', 'policy', 'gate', 'xss', 'sql'];
        
        $customKeywords = [];
        if (!empty($keywords)) {
            $customKeywords = array_filter(array_map('trim', explode(',', strtolower($keywords))));
        }

        $allKeywords = array_unique(array_merge($defaultKeywords, $customKeywords));

        $matched = [];
        $lowerAnswer = strtolower($cleanAnswer);
        foreach ($allKeywords as $kw) {
            if (!empty($kw) && str_contains($lowerAnswer, $kw)) {
                $matched[] = $kw;
            }
        }

        $matchCount = count($matched);
        $matchedCustom = !empty($customKeywords) ? array_intersect($matched, $customKeywords) : [];

        // Base score calculation
        if ($matchCount >= 4 || count($matchedCustom) >= 2) {
            $score = rand(88, 98);
            $customNote = !empty($matchedCustom) ? ' Kata kunci penting (' . implode(', ', array_slice($matchedCustom, 0, 3)) . ') terjawab dengan baik.' : '';
            $feedback = 'Jawaban sangat baik dan komprehensif! Konsep utama dijelaskan dengan tepat.' . $customNote;
            $suggestion = 'Pemahaman Anda sudah sangat solid. Terus pertahankan kedalaman analisis pada modul selanjutnya.';
        } elseif ($matchCount >= 2 || count($matchedCustom) >= 1) {
            $score = rand(72, 85);
            $feedback = 'Jawaban cukup baik dan terstruktur. Anda telah menyebutkan poin-poin utama, namun beberapa rincian teknis masih dapat diperdalam.';
            $suggestion = 'Cobalah melengkapi penjelasan dengan contoh kasus nyata atau implementasi praktis.';
        } else {
            $score = rand(45, 65);
            $feedback = 'Jawaban Anda sudah di jalur yang benar, tetapi belum mencakup kata kunci utama yang diharapkan.';
            $suggestion = 'Tinjau kembali modul materi terkait dan pastikan istilah serta konsep inti dibahas dengan jelas.';
        }

        return [
            'score' => $score,
            'feedback' => $feedback,
            'suggestion' => $suggestion
        ];
    }
}
