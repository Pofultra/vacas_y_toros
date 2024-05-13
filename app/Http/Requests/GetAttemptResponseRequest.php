<?php

namespace App\Http\Requests;

use App\Http\Requests\MakeAttemptRequest;
use App\Models\Game;

class GetAttemptResponseRequest
{
    /**
     * Get the response for a specific attempt in a game.
     *
     * @param int $gameId The ID of the game.
     * @param int $attemptNumber The number of the attempt.
     * @param string $token The API token.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the attempt, bulls, and cows.
     */
    public function getAttemptResponse($gameId, $attemptNumber, $token)
    {
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json([
                'message' => 'Game not found',
            ], 404); // Retornar código HTTP 404 Not Found
        }

        $apiToken = ($token === 'Bearer ' . $game->token);

        if (!$apiToken) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }
        //cargar el archivo de datos del juego
        $attempts = Game::getGameDataFile($game->token);
        // Verificar si el número de intento es válido
        if ($attemptNumber < 1 || $attemptNumber > count($attempts)) {
            return response()->json([
                'message' => 'Invalid attempt number',
            ], 400); // Retornar código HTTP 400 Bad Request
        }
        $attepts = Game::getGameDataFile($game->token);
        $attempt = $attempts[$attemptNumber - 1];

        $bullsAndCows = Game::calculateBullsAndCows($game->secret_code, $attempt);

        return response()->json([
            'attempt' => $attempt,
            'bulls' => $bullsAndCows['bulls'],
            'cows' => $bullsAndCows['cows'],
        ], 200);
    }
}
