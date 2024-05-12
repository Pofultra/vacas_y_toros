<?php

namespace App\Http\Requests;

use App\Http\Requests\MakeAttemptRequest;
use App\Models\Game;

class GetAttemptResponseRequest
{
    public function getAttemptResponse($gameId, $attemptNumber, $token)
    {

        $game = Game::findOrFail($gameId);
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

        // Verificar si el número de intento es válido
        if ($attemptNumber < 1 || $attemptNumber > count($game->attempts)) {
            return response()->json([
                'message' => 'Invalid attempt number',
            ], 400); // Retornar código HTTP 400 Bad Request
        }
        $attepts = Game::getGameDataFile($game->token);
        $attempt = $game->attempts[$attemptNumber - 1];

        $makeAttempt = new MakeAttemptRequest;
        $bullsAndCows = $makeAttempt->calculateBullsAndCows($game->secret_code, $attempt);

        return response()->json([
            'attempt' => $attempt,
            'bulls' => $bullsAndCows['bulls'],
            'cows' => $bullsAndCows['cows'],

        ], 200);
    }
}
