<p align="center">
  <a href="https://c4c_api.mhealthkenya.co.ke">
    <img src="https://static.wixstatic.com/media/6cf925_6eea408e5e2a45afbd22d36c3e717dbd~mv2.png/v1/fill/w_243,h_280,al_c,lg_1,q_85/c4c_new.webp" alt="c4c_api">
  </a>
  </p>

# C4C API's

### Features

c4c_api has the following modules: <br>
1) Registration: for registration of HCW (self-registration or register for someone else) <br>
2) Report exposure: individual and for someone else. <br>
3) FAQ section: Contains all frequently asked questions and their responses. <br>
4) Learning module: Contains facility based and general learning materials for HCWs. <br>
6) Broadcasting of messages
7) Immunization profile: allows updating/viewing of immunization details for the HCW
9) Checkin module: allows certain user types to checkin to a facility during their working hours
10) Annual immunization & checkup calendar- allows viewing immunization history andsetting of annual checkup date.
11) Report COVID-19 exposure: allows reporting of a COVID-19 exposure that the HCW may encounter
 
## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.8/installation#installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker). 

Clone the repository

    git clone https://github.com/mHealthKenya/c4c_api.git

Switch to the repo folder

    cd c4c_api

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Generate a new JWT authentication secret key

    php artisan jwt:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Create a public and private key in the storage folder
oauth-private.key
oauth-public.key

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone https://github.com/mHealthKenya/c4c_api.git
    cd c4c_api
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan jwt:generate 

    
**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve

## Dependencies

- [jwt-auth](https://github.com/tymondesigns/jwt-auth) - For authentication using JSON Web Tokens
- [laravel-cors](https://github.com/barryvdh/laravel-cors) - For handling Cross-Origin Resource Sharing (CORS)
- [africastalking](https://github.com/AfricasTalkingLtd/africastalking-php) - For SMS

## Folders

- `app` - Contains all the Eloquent models
- `app/Http/Controllers` - Contains all the api controllers
- `app/Http/Middleware` - Contains the JWT auth middleware
- `app/Http/Helpers` - Contains the helper functions
- `app/Http/Jobs` - Contains the functions for creating queued jobs
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Controllers/Api/UserController.php` - Contains the functions implementing the user module
- `app/Http/Controllers/Api/ResourcesController.php` - Contains the functions implementing the resource center module
- `app/Http/Controllers/Api/ImmunizationController.php` - Contains the functions implementing the immunization module
- `app/Http/Controllers/Api/ImmunizationController.php` - Contains the functions implementing the immunization module
- `app/Http/Controllers/BroadcastsController.php` - Contains the functions implementing the broadcasts module
- `app/Http/Controllers/DataController.php` - Contains the functions and queries for the highcharts
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `routes/api` - Contains all the api routes
- `tests` - Contains all the application tests

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------

# Testing API

Run the laravel development server

    php artisan serve

The api can now be accessed at

    http://localhost:8000/api

Request headers

| **Required** 	| **Key**              	| **Value**            	|
|----------	|------------------	|------------------	|
| Yes      	| Content-Type     	| application/json 	|
| Yes      	| X-Requested-With 	| XMLHttpRequest   	|
| Optional 	| Authorization    	| Token {JWT}      	|

Refer the [api specification](#api-specification) for more info.

----------
 
# Authentication
 
This applications uses JSON Web Token (JWT) to handle authentication. The token is passed with each request using the `Authorization` header with `Token` scheme. The JWT authentication middleware handles the validation and authentication of the token. Please check the following sources to learn more about JWT.
 
- https://jwt.io/introduction/
- https://self-issued.info/docs/draft-ietf-oauth-json-web-token.html
----------

# Cross-Origin Resource Sharing (CORS)
 
This applications has CORS enabled by default on all API endpoints. The default configuration allows requests from `http://localhost:3000` and `http://localhost:4200` to help speed up your frontend testing. The CORS allowed origins can be changed by setting them in the config file. Please check the following sources to learn more about CORS.
 
- https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
- https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
- https://www.w3.org/TR/cors
## Dependencies

- [laravel-cors](https://github.com/barryvdh/laravel-cors) - For handling Cross-Origin Resource Sharing (CORS)

## License

[![license](https://img.shields.io/github/license/mashape/apistatus.svg?style=for-the-badge)](#)

[![Open Source Love](https://badges.frapsoft.com/os/v2/open-source-200x33.png?v=103)](#)
