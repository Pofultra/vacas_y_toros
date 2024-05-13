
<?php

use tests\TestCase;
use App\Models\Game;


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


        $game = new Game();
        $game->id = 1;

        $evaluation = 10;

        $ranking = Game::getRanking($game, $evaluation);

        $this->assertEquals(1, $ranking);
    }
}
