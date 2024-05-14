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
            <button onclick="validateMakeAttempt()">Submit</button>

        </div>
        <div id="sub_action">

            <div id="show_attempt">
                <!-- <h2>View Attempt</h2> -->
                <!-- <label for="number_of_attempt">index:</label> -->
                <div class="index_attempt_container">

                    <input type="text" id="number_of_attempt" name="number_of_attempt" placeholder="index:"><br><br>
                    <button onclick="validateViewAttempt()">Search</button>
                </div>
            </div>

            <div id="delete_game">

                <button onclick="deleteGame()">Delete Game</button>
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