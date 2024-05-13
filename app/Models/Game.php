<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_name',
        'user_age',
        'secret_code',
        'attempts',
        'remaining_time',
        'status',
        'token',
    ];

    protected $casts = [
        'attempts' => 'array',
    ];

    public static function isGameDataFileValid($fileName)
    {
        $filePath = storage_path($fileName . '.json');
        if (!Storage::exists($filePath)) {
            return false;
        }
        $fileContent = Storage::get($filePath);
        if ($fileContent === false) {
            return false;
        }
        return true;
    }
    public static function createGameDataFile($inputString)
    {
        $fileName = storage_path($inputString . '.json');
        $fileContent = json_encode([]);

        // Check if file already exists
        if (Storage::exists($fileName)) {
            // Delete the previous file
            Storage::delete($fileName);
        }

        Storage::put($fileName, $fileContent);
    }
    public static function updateGameDataFile($fileName, $newContent)
    {
        $filePath = storage_path($fileName . '.json');

        $fileContent = json_encode($newContent);

        Storage::put($filePath, $fileContent);
    }
    public static function getGameDataFile($fileName)
    {
        $filePath = storage_path($fileName . '.json');
        if (!Storage::exists($filePath)) {
            return false;
        }
        $fileContent = Storage::get($filePath);
        $parsedContent = json_decode($fileContent, true);

        return $parsedContent;
    }
    public static function deleteGameDataFile($fileName)
    {
        $filePath = storage_path($fileName . '.json');

        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            return true;
        }

        return false;
    }

    public static function getGameByUserNameAndAge($userName, $userAge)
    {
        return self::where('user_name', $userName)
            ->where('user_age', $userAge)
            ->first();
    }
    public static function deleteAllGames()
    {
        return self::truncate();
    }

    /**
     * Generates a secret code consisting of 4 random digits.
     *
     * @return string The generated secret code.
     */
    public static function generateSecretCode()
    {
        $digits = range(0, 9);
        shuffle($digits);
        return implode('', array_slice($digits, 0, 4));
    }

    /**
     * Calculates the remaining time based on a given DateTime and remaining minutes.
     *
     * @param \DateTime $dateTime The DateTime to calculate the elapsed time from.
     * @param int $remainingMinutes The remaining minutes to calculate the remaining time for.
     * @return int The calculated remaining time in minutes.
     */
    public static function calculateRemainingTime($dateTime, $remainingSeconds)
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
    public static function calculateBullsAndCows($secretCode, $attempt)
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
    public static function isValidAttempt($attempt)
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
    public static function getRanking($game, $evaluation)
    {
        // Obtener todos los juegos ordenados por estado ('won' primero) y evaluación ascendente
        $rankedGames = Game::orderByRaw('CASE WHEN status = "won" THEN 0 ELSE 1 END, score DESC')->get();

        // Encontrar la posición del juego actual en el ranking
        $ranking = $rankedGames->search(function ($item) use ($game) {
            return $item->id === $game->id;
        }) + 1;

        return $ranking;
    }
}
