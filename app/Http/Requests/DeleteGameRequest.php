<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Game;

class DeleteGameRequest
{
    public function deleteGame($gameId)
    {
        // $token = $request->header('Authorization');

        // $apiToken = ApiToken::where('token', $token)->first();

        // if (!$apiToken) {
        //     return response()->json([
        //         'message' => 'Unauthorized',
        //     ], 401);
        // }
        $game = Game::find($gameId);
        if (!$game) {
            return response()->json([
                'message' => 'Game not found',
            ], 404);
        }

        $game->delete();

        return response()->json([
            'message' => 'Game deleted successfully',
        ], 200);
    }
}
