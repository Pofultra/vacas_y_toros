/**
 * Validates the player name and age before creating a game.
 * @returns {boolean} Returns true if all validations pass, otherwise false.
 */
function validateCreateGame() {
    var playerName = document.getElementById("player_name").value;
    var playerAge = document.getElementById("player_age").value;

    // Expresión regular para validar el nombre del jugador (solo letras y espacios)
    var nameRegex = /^[A-Za-z\s]+$/;

    // Expresión regular para validar la edad del jugador (solo números)
    var ageRegex = /^\d+$/;

    // Validar el nombre del jugador
    if (!nameRegex.test(playerName)) {
        alert("Please enter a valid player name.");
        return false;
    }

    // Validar la edad del jugador
    if (!ageRegex.test(playerAge)) {
        alert("Please enter a valid player age.");
        return false;
    }

    // Si pasa todas las validaciones, continuar con la creación del juego
    createGame();
    return true;
}

// Función para validar la entrada del usuario al hacer clic en el botón 'Submit'
/**
 * Validates the user's attempt to guess a 4-digit number.
 * @returns {boolean} Returns true if the attempt is valid, false otherwise.
 */
function validateMakeAttempt() {
    var number = document.getElementById("number").value;

    // Expresión regular para validar el número de intento (exactamente 4 dígitos)
    var numberRegex = /^\d{4}$/;

    // Validar el número de intento
    if (!numberRegex.test(number)) {
        alert("Please enter a valid 4-digit number.");
        return false;
    }

    // Si pasa la validación, continuar con el intento
    makeAttempt();
    return true;
}

// Función para validar la entrada del usuario al hacer clic en el botón 'Search'
/**
 * Validates the view attempt by checking the index entered by the user.
 * @returns {boolean} Returns true if the attempt index is valid, otherwise false.
 */
function validateViewAttempt() {
    var attemptIndex = document.getElementById("number_of_attempt").value;

    // Expresión regular para validar el índice del intento (1 o 2 dígitos)
    var indexRegex = /^\d{1,2}$/;

    // Validar el índice del intento
    if (!indexRegex.test(attemptIndex)) {
        alert("Please enter a valid index (1-99).");
        return false;
    }

    // Si pasa la validación, continuar con la búsqueda del intento
    viewAttempt();
    return true;
}
/**
 * Creates a new game by sending a POST request to the server.
 * Retrieves the response data and updates the session storage with the token, game ID, and remaining time.
 * Adds the "game-created" class to the "content" element.
 */
function createGame() {
    var playerName = document.getElementById("player_name").value;
    var playerAge = document.getElementById("player_age").value;
    fetch("/api/game", {
        method: "POST",
        body: JSON.stringify({
            user_name: playerName,
            user_age: playerAge,
        }),
        headers: {
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            // Handle the response data
            sessionStorage.setItem("token", btoa(data.api_token));
            sessionStorage.setItem("gameId", data.game_id);
            remaining_time = data.remaining_time;

            document.getElementById("content").classList.add("game-created");
        })
        .catch((error) => {
            // Handle any errors
        });
}

/**
 * Makes an attempt in the game.
 */
function makeAttempt() {
    var number = document.getElementById("number").value;
    var gameId = sessionStorage.getItem("gameId");
    var token = atob(sessionStorage.getItem("token"));
    fetch("/api/game/" + gameId, {
        method: "PUT",
        body: JSON.stringify({
            attempt: number,
        }),
        headers: {
            "Content-Type": "application/json",
            Authorization: "Bearer " + token,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            document.getElementById("number").value = "";
            if (data.message === "Game won") {
                document.getElementById("restart_game").style.display = "block";
                document.getElementById("ranking").innerText =
                    "Ranking: " + data.ranking;
                document.getElementById("status").innerText = data.status;
                alert("Congratulations! You won the game!");
            } else if (data.message === "Game Over") {
                document.getElementById("status").innerText = data.status;
                document.getElementById("ranking").innerText =
                    "Ranking: " + data.ranking;
                alert(
                    "Sorry, you lost the game. the number was " +
                        data.secret_code
                );
            }
            // Handle the response data
            document.getElementById("status").innerText = data.status;
            document.getElementById("bulls").innerText = "Bulls: " + data.bulls;
            document.getElementById("cows").innerText = "Cows: " + data.cows;
            document.getElementById("remaining_time").innerText =
                "Remaining Time: " + data.remaining_time;
            document.getElementById("evaluation").innerText =
                "Evaluation: " + data.evaluation;
            document.getElementById("ranking").innerText =
                "Ranking: " + data.ranking;
            document.getElementById("attempts").innerText +=
                "\n" + number + " b:" + data.bulls + " c:" + data.cows;
        })
        .catch((error) => {
            // Handle any errors
        });
}

/**
 * Fetches game data for a specific attempt and updates the UI with the response data.
 */
function viewAttempt() {
    var number = document.getElementById("number_of_attempt").value;
    var gameId = sessionStorage.getItem("gameId");
    var token = atob(sessionStorage.getItem("token"));
    fetch("/api/game/" + gameId + "/" + number, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            Authorization: "Bearer " + token,
        },
    })
        .then((response) => response.json())
        .then((data) => {
            // Handle the response data
            document.getElementById("bulls").innerText = "Bulls: " + data.bulls;
            document.getElementById("cows").innerText = "Cows: " + data.cows;
            document.getElementById("remaining_time").innerText =
                "Remaining Time: " + data.remaining_time;
            // document.getElementById('evaluation').innerText = 'Evaluation: ' + data.evaluation;
            // document.getElementById('ranking').innerText = 'Ranking: ' + data.ranking;
        })
        .catch((error) => {
            // Handle any errors
        });
}

function deleteGame() {
    var gameId = sessionStorage.getItem("gameId");
    var token = atob(sessionStorage.getItem("token"));
    fetch("/api/game/" + gameId, {
        method: "DELETE",
        headers: {
            Authorization: "Bearer " + token,
        },
    })
        .then((response) => {
            // Handle the response
            if (response.ok) {
                alert("Game deleted successfully");
                document
                    .getElementById("content")
                    .classList.remove("game-created");
                sessionStorage.removeItem("token");
                sessionStorage.removeItem("gameId");
                document.getElementById("status").innerText = "";
                document.getElementById("bulls").innerText = "";
                document.getElementById("cows").innerText = "";
                document.getElementById("remaining_time").innerText = "";
                document.getElementById("evaluation").innerText = "";
                document.getElementById("ranking").innerText = "";
                document.getElementById("attempts").innerText = "";
            } else {
                alert("Failed to delete game");
            }
        })
        .catch((error) => {
            // Handle any errors
        });
}

/**
 * Resets the game by clearing the input fields and removing the game-created class from the content element.
 */
function resetGame() {
    document.getElementById("player_name").value = "";
    document.getElementById("player_age").value = "";
    document.getElementById("number").value = "";
    document.getElementById("content").classList.remove("game-created");
    sessionStorage.removeItem("token");
    sessionStorage.removeItem("gameId");
    document.getElementById("status").innerText = "";
    document.getElementById("bulls").innerText = "";
    document.getElementById("cows").innerText = "";
    document.getElementById("remaining_time").innerText = "";
    document.getElementById("evaluation").innerText = "";
    document.getElementById("ranking").innerText = "";
    document.getElementById("attempts").innerText = "";
}
