
<?php

use tests\TestCase;
use App\Models\Game;
use Illuminate\Support\Str;


class GameTest extends TestCase
{

    /**
     * Test the generateSecretCode method.
     *
     * @return void
     */
    public function testGenerateSecretCode()
    {

        $secretCode = Game::generateSecretCode();

        // Verificar que el código secreto tenga 4 dígitos
        $this->assertEquals(4, strlen($secretCode));

        // Verificar que el código secreto contenga solo dígitos
        $this->assertMatchesRegularExpression('/^\d+$/', $secretCode);
    }
    public function test_calculateRemainingTime()
    {

        $dateTime = new \DateTime('2022-01-01 12:00:00');
        $remainingSeconds = 300;

        $remainingTime1 = Game::calculateRemainingTime($dateTime, $remainingSeconds);
        $remainingTime2 = Game::calculateRemainingTime(new \DateTime(), $remainingSeconds);
        $this->assertEquals(0, $remainingTime1);
        $this->assertNotEquals(0, $remainingTime2);
    }

    public function test_calculateBullsAndCows()
    {


        $secretCode = '1234';
        $attempt = '1243';

        $result = Game::calculateBullsAndCows($secretCode, $attempt);

        $this->assertEquals(2, $result['bulls']);
        $this->assertEquals(2, $result['cows']);
    }

    public function test_isValidAttempt()
    {


        $validAttempt = '1234';
        $invalidAttempt = '1122';

        $isValid = Game::isValidAttempt($validAttempt);
        $isInvalid = Game::isValidAttempt($invalidAttempt);

        $this->assertTrue($isValid);
        $this->assertFalse($isInvalid);
    }

    public function test_getRanking()
    {
        $gameA = Game::orderBy('score', 'desc')->first();
        $gameB = Game::orderBy('score', 'asc')->first();
        // Game::deleteAllGames(); // Eliminar todos los juegos
        $game1 = new Game();
        $game1->user_name = 'Ultra Test';
        $game1->user_age = 30;
        $game1->secret_code = '1234';
        $game1->remaining_time = 300;
        $game1->status = 'active';
        $game1->token = Str::random(60);
        $game1->score = $gameB->score - 1;
        $game1->save();

        $game2 = new Game();
        $game2->user_name = 'Ultra Test';
        $game2->user_age = 30;
        $game2->secret_code = '1234';
        $game2->remaining_time = 300;
        $game2->status = 'won';
        $game2->token = Str::random(60);
        $game2->score = $gameA->score + 1;
        $game2->save();

        $ranking = Game::getRanking($game2);

        $this->assertEquals(1, $ranking);
    }
}
