<?php

namespace Tests\Unit;

use App\Http\Requests\MakeAttemptRequest;
use App\Http\Requests\CreateGameRequest;
use App\Models\Game;
use Tests\TestCase;

class MakeAttemptRequestTest extends TestCase
{ 
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
        $response = $makeAttemptRequest->makeAttempt("test", ['attempt' => '1234'], 'Bearer invalid_token');

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
        //  Tomar un juego existente
        $game = Game::first();

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

    /**
     * Test makeAttempt when game is won.
     *
     * @return void
     */
    public function testMakeAttemptWhenGameWon()
    {
        // Crear un juego nuevo
        $data = [
            'user_name' => 'John Doe',
            'user_age' => 30,
        ];
        $newGameRequest = new CreateGameRequest();
        $newGameRequest->creteGame($data);
        // tomar el id y el token del juego recién creado
        $game = Game::latest()->first();
        // tomar el token de acceso
        $barear_token = 'Bearer ' . $game->token;
        // Establecer el código secreto del juego como el intento actual
        $current_attempt = $game->secret_code;


        // Crear una instancia de MakeAttemptRequest
        $makeAttemptRequest = new MakeAttemptRequest();

        // Intentar hacer un intento con el código secreto correcto
        $response = $makeAttemptRequest->makeAttempt($game->id, ['attempt' => $current_attempt], $barear_token);
        // Verificar que la respuesta sea un JsonResponse con el código 200
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        // Verificar que el mensaje de la respuesta sea "Game won"
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('Game won', $responseData['message']);
        // Verificar que el juego se haya marcado como ganado
        $game = Game::find($game->id);
        $this->assertEquals('won',$game->status);
    }
}
