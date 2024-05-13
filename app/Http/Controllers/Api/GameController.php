<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\MakeAttemptRequest;
use App\Http\Requests\DeleteGameRequest;
use App\Http\Requests\GetAttemptResponseRequest;



/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Game API",
 *      description="API endpoints for managing games",
 *      @OA\Contact(
 *          email="example@example.com"
 *      ),
 *      @OA\License(
 *          name="MIT License",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 */
class GameController extends Controller
{
    /**
     * Creates a new game.
     *     
     * 
     * @OA\Post(
     *     path="/api/game",
     *     summary="Create a new game",
     *     tags={"Games"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_name","user_age"},
     *             @OA\Property(property="user_name", type="string", example="John Doe"),
     *             @OA\Property(property="user_age", type="integer", example=30)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Game created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="game_id", type="integer"),
     *             @OA\Property(property="remaining_time", type="integer"),
     *             @OA\Property(property="api_token", type="string")
     *         )
     *     )
     * )
     */
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
     * Validates and processes a game attempt.
     *
     * @param int $gameId The ID of the game.     
     * @param string $token The authorization token.
     * 
     * @OA\Put(
     *     path="/api/game/{gameId}",
     *     summary="Make a game attempt",
     *     tags={"Games"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Parameter(
     *         name="gameId",
     *         in="path",
     *         required=true,
     *         description="ID of the game",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"attempt"},
     *             @OA\Property(property="attempt", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game attempt processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="attempt", type="string"),
     *             @OA\Property(property="bulls", type="integer"),
     *             @OA\Property(property="cows", type="integer"),
     *             @OA\Property(property="attempts_count", type="integer"),
     *             @OA\Property(property="remaining_time", type="integer"),
     *             @OA\Property(property="evaluation", type="integer"),
     *             @OA\Property(property="ranking", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid attempt",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Duplicate attempt",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=410,
     *         description="Game Over",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="secret_code", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $gameId)
    {
        $token = $request->header('Authorization');


        $validatedData = $request->validate([
            'attempt' => 'required|string',
        ]);

        $makeAttemptRequest = new MakeAttemptRequest();
        return $makeAttemptRequest->makeAttempt($gameId, $validatedData, $token);
    }

    /**
     * Delete a game.
     *
     * @param int $gameId The ID of the game to delete.
     * @param string $token The authorization token.
     * 
     * @OA\Delete(
     *     path="/api/game/{gameId}",
     *     summary="Delete a game",
     *     tags={"Games"},
     *     @OA\Parameter(
     *         name="gameId",
     *         in="path",
     *         required=true,
     *         description="ID of the game to delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy($gameId, Request $request)
    {
        $token = $request->header('Authorization');
        $deleteGameRequest = new DeleteGameRequest();
        return $deleteGameRequest->deleteGame($gameId, $token);
    }

    /**
     * Retrieves the response of a specific attempt in a game.
     *
     * @param int $gameId The ID of the game.
     * @param int $attemptNumber The number of the attempt.
     * @param string $token The authorization token.
     * 
     * @OA\Get(
     *     path="/api/game/{gameId}/attempts/{attemptNumber}",
     *     summary="Get attempt response",
     *     tags={"Games"},
     *     @OA\Parameter(
     *         name="gameId",
     *         in="path",
     *         required=true,
     *         description="ID of the game",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="attemptNumber",
     *         in="path",
     *         required=true,
     *         description="Number of the attempt",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Attempt response retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="attempt", type="string"),
     *             @OA\Property(property="bulls", type="integer"),
     *             @OA\Property(property="cows", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid attempt number",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getAttemptResponse($gameId, $attemptNumber, Request $request)
    {
        $token = $request->header('Authorization');
        $getAttemptResponseRequest = new GetAttemptResponseRequest();
        return $getAttemptResponseRequest->getAttemptResponse($gameId, $attemptNumber, $token);
    }
}
