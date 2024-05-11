<?php

namespace App\Http\Requests;

use App\Http\Requests\MakeAttemptRequest;
use App\Models\Game;

class GetAttemptResponseRequest
{
    public function getAttemptResponse($gameId, $attemptNumber)
    {
        // $token = $request->header('Authorization');

        // $apiToken = ApiToken::where('token', $token)->first();

        // if (!$apiToken) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }


        $game = Game::findOrFail($gameId);

        // Verificar si el número de intento es válido
        if ($attemptNumber < 1 || $attemptNumber > count($game->attempts)) {
            return response()->json([
                'message' => 'Invalid attempt number',
            ], 400); // Retornar código HTTP 400 Bad Request
        }

        $attempt = $game->attempts[$attemptNumber - 1];

        $makeAttempt = new MakeAttemptRequest;
        $bullsAndCows = $makeAttempt->calculateBullsAndCows($game->secret_code, $attempt);

        return response()->json([
            'attempt' => $attempt,
            'bulls' => $bullsAndCows['bulls'],
            'cows' => $bullsAndCows['cows'],
            // Otros datos relevantes...
        ], 200);
    }
}
