# Movie API

The Movie API is a RESTful web service designed for handling movie information through designated endpoints. It is constructed using the Slim PHP framework, and offers functionalities for creating, reading, updating, and deleting movie data, as well as searching, sorting and pagination.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Technologies Used](#technologies-used)
- [Contributing](#contributing)
- [License](#license)

## Features

- `GET /v1`: Get the list of all available endpoints for this API.
- `GET /v1/movies`: Get a list of all existing movies.
- `POST /v1/movies`: Add a new movie to the collection.
- `PUT /v1/movies/{uid}`: Update a movie by UID (Unique Identifier).
- `DELETE /v1/movies/{uid}`: Delete a movie by UID (Unique Identifier).
- `PATCH /v1/movies/{uid}`: Update specific data of a movie by UID (Unique Identifier).
- `GET /v1/movies/{numberPerPage}`: Get a list of movies with pagination (user defined value).
- `GET /v1/movies/{numberPerPage}/sort/{fieldToSort}`: Get a list of movies sorted by a specific field with pagination.

## Preresiquites

- PHP (>= 8.0)
- PostMan or any other http client for making request to endpoints.
- MySQL
- Composer

## Installation

- Clone: ```git clone https://github.com/demarillacizere/movie-api.git```
- Go to the project folder: `cd movie-api`
- Install composer packages: `composer install`
- Config the environment: `cp .env.example .env`
- Add environment information to `.env` file
- Set up DB
- Run the application (dev mode): `php -S localhost:8888 -t public/`

## Usage

To use the API, you can make HTTP requests to the specified endpoints using tools like curl, Postman, or any other HTTP client.

## API Documentation

The API documentation is generated using Swagger (OpenAPI). You can access the documentation by visiting the following URL in your browser: `http://localhost:8888/docs/`

This documentation provides detailed information about each endpoint, input parameters, response formats, and example requests/responses.

## Technologies Used

- PHP (>= 8.0)
- PostMan as http client for making request to endpoints.
- MySQL database for data storage.
- Composer for dependency management.
- MRC (Model-Response-Controller) design architecture.

## Contributing

Contributions are welcome! If you find a bug or want to make a helpful recommendation, please follow these steps:

1. Fork the repository.
2. Create a new branch.
3. Make your changes and test them thoroughly.
4. Create a pull request describing the changes you've made.

Alternatively, you can submit a new issue and we will make sure to take it into consideration.

## License

This project is licensed under the [MIT License](LICENSE).