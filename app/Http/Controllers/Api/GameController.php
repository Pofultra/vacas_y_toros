<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use App\Http\Requests\CreateGameRequest;
use Carbon\Carbon;
use App\Models\ApiToken;
use Illuminate\Support\Str;

class GameController extends Controller
{
    

    /**
     * Calculates the number of bulls and cows in a game attempt.
     *
     * @param string $secretCode The secret code to guess.
     * @param string $attempt The attempt made by the player.
     * @return array An array containing the number of bulls and cows.
     */
    private function calculateBullsAndCows($secretCode, $attempt)
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
        // Validar que la combinación tenga 4 dígitos y no contenga repeticiones
        $uniqueChars = array_unique(str_split($attempt));
        $uniqueString = implode('', $uniqueChars);
        return strlen($attempt) === 4 && strlen($uniqueString) === 4;
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
    public function index()
    {
        return 'Hola';
    }
    public function validateUserData(Request $request)
    {
        $validatedData = $request->validate([
            'user_name' => 'required|string',
            'user_age' => 'required|integer',
        ]);

        // Additional validation logic for user_name and user_age if needed

        return $validatedData;
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_name' => 'required|string',
            'user_age' => 'required|integer',
        ]);

        $createGameRequest = new CreateGameRequest();
        $createGameRequest->creteGame($validatedData);
    }


    /**
     * Validates a game attempt.
     *
     * @param string $attempt The attempt to validate.
     * @return bool True if the attempt is valid, false otherwise.
     */
    public function update(Request $request, $gameId)
    {
        // $token = $request->header('Authorization');

        // $apiToken = ApiToken::where('token', $token)->first();

        // if (!$apiToken) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }

        $game = Game::findOrFail($gameId);

        // Verificar si el juego ha expirado
        if ($game->remaining_time <= 0) {
            return response()->json([
                'message' => 'Game Over',
                'secret_code' => $game->secret_code,
            ], 410); // Retornar código HTTP 410 Gone
        }

        $attempt = $request->input('attempt');

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
        $game->remaining_time -= 1; // Restar 1 segundo al tiempo restante
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

    // Método para eliminar un juego existente
    public function destroy($gameId)
    {
        // $token = $request->header('Authorization');

        // $apiToken = ApiToken::where('token', $token)->first();

        // if (!$apiToken) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }
        $game = Game::findOrFail($gameId);
        $game->delete();

        return response()->json([
            'message' => 'Game deleted successfully',
        ], 200);
    }

    // Método para obtener la respuesta de un intento previo
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
        $bullsAndCows = $this->calculateBullsAndCows($game->secret_code, $attempt);

        return response()->json([
            'attempt' => $attempt,
            'bulls' => $bullsAndCows['bulls'],
            'cows' => $bullsAndCows['cows'],
            // Otros datos relevantes...
        ], 200);
    }
}
