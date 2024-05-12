<?php

namespace Tests\Unit;

use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\DeleteGameRequest;
use App\Models\Game;

use Tests\TestCase;

class DeleteGameRequestTest extends TestCase
{
    public function test_deleteGame_withValidGameIdAndToken_shouldDeleteGameAndReturnSuccessResponse()
    {
        //crete a game
        $validatedData = [
            'user_name' => 'test',
            'user_age' => 20,
        ];
        $newGame = new CreateGameRequest();
        $newGame->creteGame($validatedData);

        // Mock the Game model's find method to return the game
        $game = Game::getGameByUserNameAndAge('test', 20);

        // Arrange
        $gameId = $game->id;
        $token = 'Bearer ' . $game->token;
        $game = new Game();

        $deleteGameRequest = new DeleteGameRequest();


        // Act
        $response = $deleteGameRequest->deleteGame($gameId, $token);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull(Game::find($gameId)); // Ensure the game is deleted
    }

    public function test_deleteGame_withInvalidGameId_shouldReturnNotFoundResponse()
    {


        // Arrange
        $gameId = "invalid_game_id";
        $token = 'Bearer token';
        $deleteGameRequest = new DeleteGameRequest();

        // Act
        $response = $deleteGameRequest->deleteGame($gameId, $token);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Game not found', json_decode($response->getContent(), true)['message']);
    }

    public function test_deleteGame_withInvalidToken_shouldReturnUnauthorizedResponse()
    {

        //crete a game
        $validatedData = [
            'user_name' => 'test',
            'user_age' => 20,
        ];
        $newGame = new CreateGameRequest();
        $newGame->creteGame($validatedData);

        // Mock the Game model's find method to return the game
        $game = Game::getGameByUserNameAndAge('test', 20);

        // Arrange
        $gameId = $game->id;
        $token = "invalid_token" . $game->token;

        $deleteGameRequest = new DeleteGameRequest();

        // Act
        $response = $deleteGameRequest->deleteGame($gameId, $token);

        // Assert
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorized', json_decode($response->getContent(), true)['message']);
    }
}
