<!DOCTYPE html>
<html>

<head>
    <title>Vacas y Toros</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/js/vt.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/vt.css">
</head>

<body>
    <h1>Vacas y Toros</h1>
    @php
    session_start();
    @endphp
    <div id="content">
        <div id="game_info">
            <h2>Nota</h2>
            <p>This is a demo of the game "Vacas y Toros".</p>
            <p>The goal of the game is to guess a secret code generated by the system.</p>
            <p>The code consists of a sequence of numbers.</p>
            <p>Players will enter their guesses and the system will provide feedback in the form of "Vacas" (cows) and "Toros" (bulls).</p>
            <p>A "Vaca" represents a correct number in the wrong position, while a "Toro" represents a correct number in the correct position.</p>
            <p>Players can create a new game by entering their name and age, and then start the game.</p>
            <p>Once the game is started, players can make attempts by entering a number and clicking the "Submit" button.</p>
            <p>The system will provide feedback on each attempt, indicating the number of "Vacas" and "Toros" obtained.</p>
            <p>Players can delete the current game or restart it at any time.</p>
        </div>

        <div id="create_game">
            <h2>Create Game</h2>
            <label for="player_name">Player Name:</label>
            <input type="text" id="player_name" name="player_name"><br><br>
            <label for="player_age">Player Age:</label>
            <input type="text" id="player_age" name="player_age"><br><br>
            <button onclick="validateCreateGame()">Start Game</button>
        </div>
        <div id="make_attempt">

            <h2>Make Attempt</h2>
            <label for="number">Number:</label>
            <input type="text" id="number" name="number"><br><br>
            <button id="attempt_button" onclick="validateMakeAttempt()">Submit</button>

        </div>
        <div id="sub_action">
            <div id="delete_game">

                <button onclick="deleteGame()">Delete Game</button>
            </div>
            <div id="restart_game" hidden>

                <button onclick="resetGame()">Restart Game</button>
            </div>
        </div>
    </div>
    <div id="sidebar">
        <h2>Game Information</h2>
        <div id="cronometer">
            <p id="remaining_time"></p>
        </div>
        <div id="game_status">
            <p id="status"></p>

        </div>
        <div id="cows_bulls">

            <p id="bulls"></p>
            <p id="cows"></p>
        </div>
        <div id="score">


            <p id="attempts"></p>
            <p id="evaluation"></p>
            <p id="ranking"></p>
        </div>



    </div>



</body>

</html>