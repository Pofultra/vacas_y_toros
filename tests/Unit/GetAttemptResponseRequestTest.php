<?php

namespace Tests\Unit;

use Illuminate\Support\Str;
use Tests\TestCase;
use App\Http\Requests\GetAttemptResponseRequest;
use App\Http\Requests\MakeAttemptRequest;
use App\Models\Game;

class GetAttemptResponseRequestTest extends TestCase
{
    /**
     * Test getAttemptResponse when game is not found.
     *
     * @return void
     */
    public function testGetAttemptResponseWhenGameNotFound()
    {
        // Create a new instance of GetAttemptResponseRequest
        $getAttemptResponseRequest = new GetAttemptResponseRequest();

        // Call the getAttemptResponse method with invalid game ID
        $response = $getAttemptResponseRequest->getAttemptResponse(999, 1, 'Bearer valid_token');

        // Verify that the response is a JsonResponse with status code 404
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        // Verify that the response message is "Game not found"
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Game not found', $responseData['message']);
    }

    /**
     * Test getAttemptResponse when unauthorized.
     *
     * @return void
     */
    public function testGetAttemptResponseWhenUnauthorized()
    {

        // Create a new game
        $game = Game::create([
            'token' => Str::random(60),
            'user_name' => 'Ultra Test',
            'user_age' => 30,
            'secret_code' => Game::generateSecretCode(),
            'remaining_time' => 300,
            'status' => 'in_progress',
        ]);

        // Create a new instance of GetAttemptResponseRequest
        $getAttemptResponseRequest = new GetAttemptResponseRequest();

        // Call the getAttemptResponse method with invalid token
        $response = $getAttemptResponseRequest->getAttemptResponse($game->id, 1, 'Bearer invalid_token');

        // Verify that the response is a JsonResponse with status code 401
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        // Verify that the response message is "Unauthorized"
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Unauthorized', $responseData['message']);
    }

    /**
     * Test getAttemptResponse with valid parameters.
     *
     * @return void
     */
    public function testGetAttemptResponseWithValidParameters()
    {
        // Create a new game

        $game = Game::create([
            'token' => Str::random(60),
            'user_name' => 'Ultra Test',
            'user_age' => 30,
            'secret_code' => Game::generateSecretCode(),
            'remaining_time' => 300,
            'status' => 'in_progress',
        ]);

        // Make an attempt
        $makeAttemptRequest = new MakeAttemptRequest();
        $makeAttemptRequest->makeAttempt($game->id, ['attempt' => "1234"], 'Bearer ' . $game->token);

        // Create a new instance of GetAttemptResponseRequest
        $getAttemptResponseRequest = new GetAttemptResponseRequest();

        // Call the getAttemptResponse method with valid parameters
        $response = $getAttemptResponseRequest->getAttemptResponse($game->id, 1, 'Bearer ' . $game->token);

        // Verify that the response is a JsonResponse with status code 200
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        // Verify that the response contains the attempt, bulls, and cows
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('attempt', $responseData);
        $this->assertArrayHasKey('bulls', $responseData);
        $this->assertArrayHasKey('cows', $responseData);
    }
}
