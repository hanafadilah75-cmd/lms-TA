<?php

namespace Tests\Feature;

use App\Services\GroqService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GroqServiceTest extends TestCase
{
    public function test_fallback_grading_when_no_api_key(): void
    {
        // Force API key to be empty
        config(['services.groq.api_key' => null]);
        
        $service = new GroqService();
        $response = $service->reviewEssay('Apa itu ORM?', 'ORM adalah Object-Relational Mapping yang memetakan database ke objek.');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('score', $response);
        $this->assertArrayHasKey('feedback', $response);
        $this->assertArrayHasKey('suggestion', $response);
        
        // Assert that the score is a fallback score based on our formula
        $this->assertGreaterThanOrEqual(55, $response['score']);
    }

    public function test_api_grading_when_api_key_configured(): void
    {
        // Mock the HTTP request to Groq API
        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score' => 95,
                                'feedback' => 'Jawaban sangat tepat.',
                                'suggestion' => 'Pertahankan pemahaman Anda.'
                            ])
                        ]
                    ]
                ]
            ], 200)
        ]);

        // Temporarily set a dummy API key
        config(['services.groq.api_key' => 'gsk_test12345']);

        $service = new GroqService();
        $response = $service->reviewEssay('Apa itu ORM?', 'ORM adalah Object-Relational Mapping.');

        $this->assertIsArray($response);
        $this->assertEquals(95, $response['score']);
        $this->assertEquals('Jawaban sangat tepat.', $response['feedback']);
        $this->assertEquals('Pertahankan pemahaman Anda.', $response['suggestion']);
    }
}
