<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Requests\CreateGameRequest;

class CreateGameRequestTest extends TestCase
{


    /**
     * Test the createGame method.
     *
     * @return void
     */
    public function testCreateGame()
    {

        // Simular datos validados
        $validatedData = [
            'user_name' => 'Ultra Test',
            'user_age' => 30,
        ];

        $createGameRequest = new CreateGameRequest();
        $response = $createGameRequest->creteGame($validatedData);

        // Verificar que la respuesta sea un JsonResponse con el código 201
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());

        // Verificar que la respuesta contenga los datos esperados
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('game_id', $responseData);
        $this->assertArrayHasKey('remaining_time', $responseData);
        $this->assertArrayHasKey('api_token', $responseData);
    }
}
