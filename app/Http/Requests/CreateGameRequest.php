<?php

namespace App\Http\Requests;

use App\Models\Game;

use Illuminate\Support\Str;

class CreateGameRequest
{
    /**
     * Creates a new game.
     *
     * @param array $validatedData The validated data from the request.
     * @return \Illuminate\Http\JsonResponse The response with the game data.
     */
    public function creteGame($validatedData)
    {


        // Generar el código secreto
        $secretCode = Game::generateSecretCode();

        // Calcular el tiempo restante
        $maxTime = env('GAME_MAX_TIME');
        $remainingTime = $maxTime;

        // Crear el nuevo juego
        $game = Game::create([
            'token' => Str::random(60),
            'user_name' => $validatedData['user_name'],
            'user_age' => $validatedData['user_age'],
            'secret_code' => $secretCode,
            'remaining_time' => $remainingTime,
            'status' => 'in_progress',
        ]);

        return response()->json([
            'game_id' => $game->id,
            'remaining_time' => $remainingTime,
            'api_token' => $game->token,
        ], 201);
    }
}
