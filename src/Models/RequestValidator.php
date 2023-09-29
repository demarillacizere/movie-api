<?php
namespace MovieApi\Models;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Laminas\Diactoros\Response\JsonResponse;
class RequestValidator
{
    public static function validateMovieData(array $data): array
    {
        $validatedData = [];

        try {
            // Validate and sanitize the 'title' field
            self::assertFieldExists($data, 'title');
            Assertion::notEmpty($data['title'], 'Title is required');
            $validatedData['title'] = htmlspecialchars($data['title']);

            // Validate and sanitize the 'year' field
            self::assertFieldExists($data, 'year');
            Assertion::integer($data['year'], 'Year must be an integer');
            $validatedData['year'] = (filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT));

            // Validate and sanitize the 'released' field (assuming it's a date)
            self::assertFieldExists($data, 'released');
            Assertion::string($data['released'], 'Y-m-d', 'Invalid date format for released');
            $validatedData['released'] = htmlspecialchars($data['released']);

            self::assertFieldExists($data, 'runtime');
            Assertion::notEmpty($data['runtime'], 'Run time is required');
            $validatedData['runtime'] = htmlspecialchars($data['runtime']);

            self::assertFieldExists($data, 'genre');
            Assertion::notEmpty($data['genre'], 'Genre is required');
            $validatedData['genre'] = htmlspecialchars($data['genre']);

            self::assertFieldExists($data, 'director');
            Assertion::notEmpty($data['director'], 'Director is required');
            $validatedData['director'] = htmlspecialchars($data['director']);

            self::assertFieldExists($data, 'actors');
            Assertion::notEmpty($data['actors'], 'Actors are required');
            $validatedData['actors'] = htmlspecialchars($data['actors']);

            self::assertFieldExists($data, 'country');
            Assertion::notEmpty($data['country'], 'Country is required');
            $validatedData['country'] = htmlspecialchars($data['country']);

            self::assertFieldExists($data, 'type');
            Assertion::notEmpty($data['type'], 'Type is required');
            $validatedData['type'] = htmlspecialchars($data['type']);

            self::assertFieldExists($data, 'poster');
            Assertion::notEmpty($data['poster'], 'Poster url is required');
            Assertion::url($data['poster'], 'Poster url is invalid');
            $validatedData['poster'] = htmlspecialchars($data['poster']);

            self::assertFieldExists($data, 'imdb');
            Assertion::integer($data['year'], 'Imdb rating must be an integer');
            $validatedData['imdb'] = (filter_var($data['imdb'], FILTER_SANITIZE_NUMBER_INT));

            // Additional assertions and sanitization for other fields...
        } catch (\InvalidArgumentException $e) {
            // If any validation assertion fails, throw an exception with the error message
            throw new \InvalidArgumentException($e->getMessage());
        }
        return $validatedData;
    }

    public static function assertFieldExists(array $data, string $fieldName)
    {
        if (!isset($data[$fieldName])) {
            throw new \InvalidArgumentException("Field '{$fieldName}' is missing in the request body.");
        }
    }
}
