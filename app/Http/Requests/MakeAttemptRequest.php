<?php

namespace App\Http\Requests;

use App\Models\Game;

class MakeAttemptRequest
{
    /**
     * Makes an attempt in the game.
     *
     * @param int $gameId The ID of the game.
     * @param array $validatedData The validated data from the request.
     * @param string $token The API token.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the result of the attempt.
     */

    public function makeAttempt($gameId, $validatedData, $token)
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

        // Verificar si el juego ha expirado
        $remainingTime = Game::calculateRemainingTime($game->created_at, $game->remaining_time);
        if ($remainingTime <= 0) {
            $game->status = 'expired';
            $game->update();
            Game::deleteGameDataFile($game->token); // Eliminar datos del juego
            return response()->json([
                'message' => 'Game Over',
                'secret_code' => $game->secret_code,
            ], 410); // Retornar código HTTP 410 Gone
        }

        $attempt = $validatedData['attempt'];

        // Verificar si la combinación es válida
        if (!Game::isValidAttempt($attempt)) {
            return response()->json([
                'message' => 'Invalid attempt',
            ], 400); // Retornar código HTTP 400 Bad Request
        }

        // Verificar si el intento ya existe en las variables de Redis
        if (Game::isGameDataFileValid($game->token)) {
            $attempts = Game::getGameDataFile($game->token);
        } else {
            $attempts = [];
        }

        // Verificar si la combinación ya ha sido intentada
        if (in_array($attempt, $attempts)) {
            return response()->json([
                'message' => 'Duplicate attempt',
            ], 409); // Retornar código HTTP 409 Conflict
        }

        // Calcular toros y vacas
        $bullsAndCows = Game::calculateBullsAndCows($game->secret_code, $attempt);

        // Actualizar los intentos y el tiempo restante

        $attempts[] = $attempt;
        Game::updateGameDataFile($game->token, $attempts); // Actualizar los intentos en las variables de sesión
        $game->remaining_time = $remainingTime; // Actualizar el tiempo restante
        $game->update();

        // Calcular la evaluación
        $evaluation = intval($game->remaining_time / 2) + count($attempts);

        // Obtener el ranking del juego
        $ranking = Game::getRanking($game, $evaluation);

        // Verificar si se ha ganado el juego
        if ($bullsAndCows['bulls'] === 4) {
            $game->status = 'won';
            $game->score = $evaluation;

            $game->update();
            Game::deleteGameDataFile($game->token); // Eliminar datos del juego
            return response()->json([
                'message' => 'Game won',
                'secret_code' => $game->secret_code,
                'ranking' => $ranking,
            ], 200); // Retornar código HTTP 200 OK

        }

        return response()->json([
            'attempt' => $attempt,
            'bulls' => $bullsAndCows['bulls'],
            'cows' => $bullsAndCows['cows'],
            'attempts_count' => count($attempts),
            'remaining_time' => $game->remaining_time,
            'evaluation' => $evaluation,
            'ranking' => $ranking,
        ], 200);
    }
}
