<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\MakeAttemptRequest;
use App\Http\Requests\DeleteGameRequest;
use App\Http\Requests\GetAttemptResponseRequest;
use App\Models\ApiToken;



class GameController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_name' => 'required|string',
            'user_age' => 'required|integer',
        ]);

        $createGameRequest = new CreateGameRequest();
        return $createGameRequest->creteGame($validatedData);
    }


    /**
     * Validates a game attempt.
     *
     * @param string $attempt The attempt to validate.
     * @return bool True if the attempt is valid, false otherwise.
     */
    public function update(Request $request, $gameId)
    {
        $token = $request->header('Authorization');

        // $apiToken = ApiToken::where('token', $token)->first();

        // if (!$apiToken) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }
        $validatedData = $request->validate([
            'attempt' => 'required|string',
        ]);

        $makeAttemptRequest = new MakeAttemptRequest();
        return $makeAttemptRequest->makeAttempt($gameId, $validatedData, $token);
    }

    // Método para eliminar un juego existente
    public function destroy($gameId)
    {
        $deleteGameRequest = new DeleteGameRequest();
        return $deleteGameRequest->deleteGame($gameId);
    }

    // Método para obtener la respuesta de un intento previo
    public function getAttemptResponse($gameId, $attemptNumber, Request $request)
    {
        $token = $request->header('Authorization');
        $getAttemptResponseRequest = new GetAttemptResponseRequest();
        return $getAttemptResponseRequest->getAttemptResponse($gameId, $attemptNumber, $token);
    }
}
