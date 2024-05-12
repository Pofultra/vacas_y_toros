<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Requests\CreateGameRequest;

class CreateGameRequestTest extends TestCase
{
    /**
     * Test the generateSecretCode method.
     *
     * @return void
     */
    public function testGenerateSecretCode()
    {
        $createGameRequest = new CreateGameRequest();
        $secretCode = $createGameRequest->generateSecretCode();

        // Verificar que el código secreto tenga 4 dígitos
        $this->assertEquals(4, strlen($secretCode));

        // Verificar que el código secreto contenga solo dígitos
        $this->assertMatchesRegularExpression('/^\d+$/', $secretCode);
    }

    /**
     * Test the createGame method.
     *
     * @return void
     */
    public function testCreateGame()
    {
        // Simular datos validados
        $validatedData = [
            'user_name' => 'John Doe',
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
