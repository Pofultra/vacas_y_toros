<?php

namespace Tests\Unit;

use App\Http\Requests\MakeAttemptRequest;
use App\Models\Game;
use Tests\TestCase;

class MakeAttemptRequestTest extends TestCase
{
    public function test_calculateRemainingTime()
    {
        $request = new MakeAttemptRequest();

        $dateTime = new \DateTime('2022-01-01 12:00:00');
        $remainingSeconds = 300;

        $remainingTime1 = $request->calculateRemainingTime($dateTime, $remainingSeconds);
        $remainingTime2 = $request->calculateRemainingTime(new \DateTime(), $remainingSeconds);
        $this->assertEquals(0, $remainingTime1);
        $this->assertNotEquals(0, $remainingTime2);
    }

    public function test_calculateBullsAndCows()
    {
        $request = new MakeAttemptRequest();

        $secretCode = '1234';
        $attempt = '1243';

        $result = $request->calculateBullsAndCows($secretCode, $attempt);

        $this->assertEquals(2, $result['bulls']);
        $this->assertEquals(2, $result['cows']);
    }

    public function test_isValidAttempt()
    {
        $request = new MakeAttemptRequest();

        $validAttempt = '1234';
        $invalidAttempt = '1122';

        $isValid = $request->isValidAttempt($validAttempt);
        $isInvalid = $request->isValidAttempt($invalidAttempt);

        $this->assertTrue($isValid);
        $this->assertFalse($isInvalid);
    }

    public function test_getRanking()
    {
        $request = new MakeAttemptRequest();

        $game = new Game();
        $game->id = 1;

        $evaluation = 10;

        $ranking = $request->getRanking($game, $evaluation);

        $this->assertEquals(1, $ranking);
    }

    /**
     * Test makeAttempt when game is not found.
     *
     * @return void
     */
    public function testMakeAttemptWhenGameNotFound()
    {
        // Crear una instancia de MakeAttemptRequest
        $makeAttemptRequest = new MakeAttemptRequest();

        // Intentar hacer un intento con un ID de juego inexistente
        $response = $makeAttemptRequest->makeAttempt(999, [], 'Bearer invalid_token');

        // Verificar que la respuesta sea un JsonResponse con el código 404
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());

        // Verificar que el mensaje de la respuesta sea "Game not found"
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Game not found', $responseData['message']);
    }

    /**
     * Test makeAttempt when unauthorized.
     *
     * @return void
     */
    public function testMakeAttemptWhenUnauthorized()
    {
        // Crear un juego nuevo
        $game = Game::factory()->create();

        // Crear una instancia de MakeAttemptRequest
        $makeAttemptRequest = new MakeAttemptRequest();

        // Intentar hacer un intento con un token de autorización inválido
        $response = $makeAttemptRequest->makeAttempt($game->id, [], 'Bearer invalid_token');

        // Verificar que la respuesta sea un JsonResponse con el código 401
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());

        // Verificar que el mensaje de la respuesta sea "Unauthorized"
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Unauthorized', $responseData['message']);
    }
}
