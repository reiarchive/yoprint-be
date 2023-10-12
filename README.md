# App Usage Guide

This readme.md file provides a step-by-step guide on how to use the application. To run the app successfully, please follow the instructions below.

## Prerequisites

Before you can use the app, make sure you have the following prerequisites installed on your system:

1. [PHP](https://www.php.net/) (>= 8.0)
2. [Composer](https://getcomposer.org/)
3. [Node.js](https://nodejs.org/)
4. [npm](https://www.npmjs.com/get-npm)
5. [Redis](https://redis.io/) (for queue processing)

## Installation

1. Clone the repository:

   ```shell
   git clone https://github.com/reiarchive/yoprint-be.git
   ```

2. Navigate to the project directory:

   ```shell
   cd yoprint-be
   ```

3. Install PHP dependencies:

   ```shell
   composer install
   ```

4. Install JavaScript dependencies:

   ```shell
   npm install
   ```

## Running the Application

To run the application, you need to execute several commands. Make sure you are in the project directory in your terminal.

1. Start the Laravel development server:

   ```shell
   php artisan serve
   ```

2. Start the WebSocket server

   ```shell
   php artisan websocket:serve
   ```

3. Compile the front-end assets:

   ```shell
   npm run dev
   ```

4. Start the queue worker for background job processing (using Redis in this example):

   ```shell
   php artisan queue:work redis --queue=default --tries=10 --timeout=3000 --memory=8000
   ```

   Adjust the options as needed for your specific project requirements.

## Accessing the Application

Once you have completed the above steps, you should be able to access and use the application by visiting the URL provided by the `php artisan serve` command (usually `http://localhost:8000`).