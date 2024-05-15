# Vacas y Toros

## Description

This is a Laravel-based project that includes a game with various features. The game logic is handled by the GameController class.

The GameController class has five methods:

    store(Request $request): This method is used to create a new game. It validates the incoming request data to ensure that user_name and user_age are provided, then creates a new CreateGameRequest object and calls its creteGame method with the validated data.

    update(Request $request, $gameId): This method is used to make an attempt in a game. It validates the incoming request data to ensure that attempt is provided, then creates a new MakeAttemptRequest object and calls its makeAttempt method with the game ID, the validated data, and the authorization token from the request header.

    destroy($gameId, Request $request): This method is used to delete a game. It creates a new DeleteGameRequest object and calls its deleteGame method with the game ID and the authorization token from the request header.

    getAttemptResponse($gameId, $attemptNumber, Request $request): This method is used to get the response of a specific attempt in a game. It creates a new GetAttemptResponseRequest object and calls its getAttemptResponse method with the game ID, the attempt number, and the authorization token from the request header.

    Each method is annotated with OpenAPI (formerly Swagger) annotations to document the API endpoints, their parameters, and their responses.

## Demo View

To showcase the game, there is a demo view called `vt.blade.php`. This view provides a visual representation of the game and allows users to interact with it. It can be found in the `resources/views` directory of the project.

To access the demo view, navigate to `http://localhost:8000/vt` after starting the local development server.

The `vt.blade.php` view utilizes the GameController class to handle the game logic and display the necessary information to the user.

Feel free to explore the demo view and interact with the game to get a better understanding of its features and functionality.

## Features

-   Simple logic.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

## Prerequisites

-   PHP 7.4 or higher
-   Composer
-   Laravel 8.x
-   A SQL database (SQLite)

## Installation

1. Clone the repository:

    git clone https://github.com/Pofultra/vacas_y_toros.git

2. Install PHP dependencies:

    composer install

3. Add to .env:

    GAME_MAX_TIME=600

4. Generate a new application key

    php artisan key:generate

5. Run the database migrations

    php artisan migrate

6. Start the local development server

    php artisan serve

You should now be able to access the application at localhost:8000.

## Usage

The main functionality of the application is handled by the GameController class. This includes methods for starting a new game, making an attempt, and getting the response for an attempt.

## API Documentation

You can find the API documentation at [API Documentation](/api/documentation).

## Running the tests

You can run the tests for this application using PHPUnit:

    vendor/bin/phpunit

## Acknowledgments
Laravel
Composer

