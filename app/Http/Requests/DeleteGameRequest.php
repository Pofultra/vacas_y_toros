<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Game;

class DeleteGameRequest
{
    /**
     * Delete a game.
     *
     * @param int $gameId The ID of the game to delete.
     * @param string $token The token used for authorization.
     * @return \Illuminate\Http\JsonResponse The JSON response indicating the result of the deletion.
     */
    public function deleteGame($gameId, $token)
    {
        $game = Game::find($gameId);

        if (!$game) {
            return response()->json([
                'message' => 'Game not found',
            ], 404);
        }

        $apiToken = ($token === 'Bearer ' . $game->token);

        if (!$apiToken) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $game->delete();

        return response()->json([
            'message' => 'Game deleted successfully',
        ], 200);
    }
}
