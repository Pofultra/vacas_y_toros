<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Game;

class MakeAttemptRequest
{

    /**
     * Calculates the remaining time based on a given DateTime and remaining minutes.
     *
     * @param \DateTime $dateTime The DateTime to calculate the elapsed time from.
     * @param int $remainingMinutes The remaining minutes to calculate the remaining time for.
     * @return int The calculated remaining time in minutes.
     */
    public function calculateRemainingTime($dateTime, $remainingSeconds)
    {
        $currentTime = new \DateTime();
        $elapsedTime = $dateTime->diff($currentTime);
        $elapsedSeconds = ($elapsedTime->days * 24 * 60 * 60) + ($elapsedTime->h * 60 * 60) + ($elapsedTime->i * 60) + $elapsedTime->s;

        if ($elapsedSeconds > $remainingSeconds) {
            return 0;
        } else {
            $remainingTime = $remainingSeconds - $elapsedSeconds;
            return $remainingTime;
        }
    }
    /**
     * Calculates the number of bulls and cows in a game attempt.
     *
     * @param string $secretCode The secret code to guess.
     * @param string $attempt The attempt made by the player.
     * @return array An array containing the number of bulls and cows.
     */
    public function calculateBullsAndCows($secretCode, $attempt)
    {
        $bulls = 0;
        $cows = 0;

        for ($i = 0; $i < 4; $i++) {
            if ($secretCode[$i] === $attempt[$i]) {
                $bulls++;
            } elseif (strpos($secretCode, $attempt[$i]) !== false) {
                $cows++;
            }
        }

        return [
            'bulls' => $bulls,
            'cows' => $cows,
        ];
    }
    /**
     * Gets the ranking of a game based on the evaluation.
     *
     * @param Game $game The game to evaluate.
     * @param int $evaluation The evaluation of the game.
     * @return string The ranking of the game.
     */
    private function isValidAttempt($attempt)
    {
        // Validar que la combinación tenga 4 dígitos, no contenga repeticiones y sean enteros
        $uniqueChars = array_unique(str_split($attempt));
        $uniqueString = implode('', $uniqueChars);
        return strlen($attempt) === 4 && strlen($uniqueString) === 4 && ctype_digit($attempt);
    }
    /**
     * Gets the ranking of a game based on the evaluation.
     *
     * @param Game $game The game to evaluate.
     * @param int $evaluation The evaluation of the game.
     * @return int The ranking of the game.
     */
    private function getRanking($game, $evaluation)
    {
        // Obtener todos los juegos ordenados por estado ('won' primero) y evaluación ascendente
        $rankedGames = Game::orderByRaw('CASE WHEN status = "won" THEN 0 ELSE 1 END, score ASC')->get();

        // Encontrar la posición del juego actual en el ranking
        $ranking = $rankedGames->search(function ($item) use ($game) {
            return $item->id === $game->id;
        }) + 1;

        return $ranking;
    }
    public function makeAttempt($gameId, $validatedData)
    {


        $game = Game::findOrFail($gameId);

        // Verificar si el juego ha expirado
        $remainingTime = $this->calculateRemainingTime($game->created_at, $game->remaining_time);
        if ($remainingTime <= 0) {
            return response()->json([
                'message' => 'Game Over',
                'secret_code' => $game->secret_code,
            ], 410); // Retornar código HTTP 410 Gone
        }

        $attempt = $validatedData['attempt'];

        // Verificar si la combinación es válida
        if (!$this->isValidAttempt($attempt)) {
            return response()->json([
                'message' => 'Invalid attempt',
            ], 400); // Retornar código HTTP 400 Bad Request
        }

        // Verificar si la combinación ya ha sido intentada
        if (in_array($attempt, $game->attempts)) {
            return response()->json([
                'message' => 'Duplicate attempt',
            ], 409); // Retornar código HTTP 409 Conflict
        }

        // Calcular toros y vacas
        $bullsAndCows = $this->calculateBullsAndCows($game->secret_code, $attempt);

        // Actualizar los intentos y el tiempo restante
        $attempts = $game->attempts;
        $attempts[] = $attempt;
        $game->attempts = $attempts;
        $game->remaining_time = $remainingTime; // Actualizar el tiempo restante
        $game->update();

        // Calcular la evaluación
        $evaluation = intval($game->remaining_time / 2) + count($game->attempts);

        // Obtener el ranking del juego
        $ranking = $this->getRanking($game, $evaluation);

        // Verificar si se ha ganado el juego
        if ($bullsAndCows['bulls'] === 4) {
            $game->status = 'won';
            $game->save();
        }

        return response()->json([
            'attempt' => $attempt,
            'bulls' => $bullsAndCows['bulls'],
            'cows' => $bullsAndCows['cows'],
            'attempts_count' => count($game->attempts),
            'remaining_time' => $game->remaining_time,
            'evaluation' => $evaluation,
            'ranking' => $ranking,
        ], 200);
    }
}
