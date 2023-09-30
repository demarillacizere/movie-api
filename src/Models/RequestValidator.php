<?php
namespace MovieApi\Models;

use Assert\Assertion;
use Assert\AssertionFailedException;
use Laminas\Diactoros\Response\JsonResponse;
use Fig\Http\Message\StatusCodeInterface;

class RequestValidator
{

    public static function ValidateAndSanitizeFields(array $data): array
    {
        try {

            if (isset($data['title'])) {
                Assertion::notEmpty($data['title'], 'Title is required');
                $validatedData['title'] = htmlspecialchars($data['title']);

            }
            if (isset($data['year'])) {
                Assertion::integer($data['year'], 'Year must be an integer');
                $validatedData['year'] = (filter_var($data['year'], FILTER_SANITIZE_NUMBER_INT));
            }

            if (isset($data['released'])) {
                Assertion::string($data['released'], 'Y-m-d', 'Invalid date format for released');
                $validatedData['released'] = htmlspecialchars($data['released']);
            }
            if (isset($data['runtime'])) {
                Assertion::notEmpty($data['runtime'], 'Run time is required');
                $validatedData['runtime'] = htmlspecialchars($data['runtime']);
            }
            if (isset($data['genre'])) {
                Assertion::notEmpty($data['genre'], 'Genre is required');
                $validatedData['genre'] = htmlspecialchars($data['genre']);
            }

            if (isset($data['director'])) {
                Assertion::notEmpty($data['director'], 'Director is required');
                $validatedData['director'] = htmlspecialchars($data['director']);
            }

            if (isset($data['actors'])) {
                Assertion::notEmpty($data['actors'], 'Actors are required');
                $validatedData['actors'] = htmlspecialchars($data['actors']);
            }
            if (isset($data['country'])) {
                Assertion::notEmpty($data['country'], 'Country is required');
                $validatedData['country'] = htmlspecialchars($data['country']);
            }

            if (isset($data['type'])) {
                Assertion::notEmpty($data['type'], 'Type is required');
                $validatedData['type'] = htmlspecialchars($data['type']);
            }

            if (isset($data['poster'])) {
                Assertion::notEmpty($data['poster'], 'Poster url is required');
                Assertion::url($data['poster'], 'Poster url is invalid');
                $validatedData['poster'] = htmlspecialchars($data['poster']);
            }
            if (isset($data['imdb'])) {
                Assertion::numeric($data['imdb'], 'Imdb rating must be a number.');
                $validatedData['imdb'] = $data['imdb'];
            }
            return $validatedData;
        } catch (\InvalidArgumentException $e) {
            // If there's a validation error, re-throw the exception
            throw $e;
        }
    }
    public static function PostAndPutValidation(array $data): array
    {
        $validatedData = [];
        try {
            self::assertFieldExists($data, 'title');
            self::assertFieldExists($data, 'year');
            self::assertFieldExists($data, 'released');
            self::assertFieldExists($data, 'runtime');
            self::assertFieldExists($data, 'genre');
            self::assertFieldExists($data, 'director');
            self::assertFieldExists($data, 'actors');
            self::assertFieldExists($data, 'country');
            self::assertFieldExists($data, 'type');
            self::assertFieldExists($data, 'poster');
            self::assertFieldExists($data, 'imdb');
            $validatedData = self::ValidateAndSanitizeFields($data);
            return $validatedData;
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }

    }

    public static function assertFieldExists(array $data, string $fieldName)
    {
        if (!isset($data[$fieldName])) {
            throw new \InvalidArgumentException("Field '{$fieldName}' is missing in the request body.");
        }
    }
}