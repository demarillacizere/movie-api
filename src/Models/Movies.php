<?php

namespace MovieApi\Models;

use DI\Container;
use DI\NotFoundException;
use Exception;

class Movies extends A_Model
{
    public int $id;
    public string $title;
    public int $year;
    public string $released;
    public string $runtime;
    public string $genre;
    public string $director;
    public string $actors;
    public string $country;
    public string $poster;
    public float $imdb;
    public string $type;

    private string $dbTableName = 'movies';

    function findAll(): array
    {
        $sql = "SELECT * FROM " . $this->dbTableName;
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute();
            $posts = $stm->fetchAll();
            return $posts;
        } catch (\PDOException $exception) {
            throw $exception;
        }

    }

    function findById(int $id): ?array
    {
        $sql = "SELECT * FROM " . $this->dbTableName . " WHERE uid = ?";

        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute([$id]);
            $result = $stm->fetch();

            if ($result === false) {
                return null;
            }
            return $result;
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }

    public function findByNumberPerPage($numberPerPage): array
    {
        $sql = "SELECT * FROM " . $this->dbTableName . " LIMIT ?";
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute([$numberPerPage]);
            $movies = $stm->fetchAll();
        } catch (\PDOException $exception) {
            throw $exception;
        }
        return $movies;
    }

    public function findByNumberPerPageAndSort($numberPerPage, $fieldToSort): array
    {
        $sql = "SELECT * FROM " . $this->dbTableName . " ORDER BY $fieldToSort LIMIT ?";
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute([$numberPerPage]);

            // Fetch the record as an associative array
            $movies = $stm->fetchAll();
        } catch (\PDOException $exception) {
            throw $exception;
        }
        return $movies;
    }
    function insert(array $data): int
    {
        $sql = "INSERT INTO " . $this->dbTableName . " (title, year, released, runtime, genre, director, actors, country, poster, imdb, type) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute(
                [
                    $data[0],
                    $data[1],
                    $data[2],
                    $data[3],
                    $data[4],
                    $data[5],
                    $data[6],
                    $data[7],
                    $data[8],
                    $data[9],
                    $data[10]
                ]
            );
            return $this->getPdo()->lastInsertId();
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }
    function update($id, array $data): bool
    {
        $existingMovie = $this->findById($id);

        if ($existingMovie === null) {
            throw new NotFoundException;
        }
        $sql = "UPDATE " . $this->dbTableName . " SET title=?, year=?, released=?, runtime=?, genre=?, director=?, actors=?, country=?, poster=?, imdb=?, type=? WHERE uid=?";
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute([$data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $id]);
            $rowCount = $stm->rowCount();
            return $rowCount > 0; // Return true if any rows were affected, false otherwise
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }

    function patch($id, $data): bool
    {
        $existingMovie = $this->findById($id);

        if ($existingMovie === null) {
            throw new NotFoundException;
        }
        $sql = "UPDATE " . $this->dbTableName . " SET ";
        $params = [];
        foreach ($data as $field => $value) {
            $sql .= "$field = ?, ";
            $params[] = $value;
        }
        // Remove the trailing comma and add the WHERE clause
        $sql = rtrim($sql, ', ') . " WHERE uid = ?";
        // Add the movie ID to the parameters
        $params[] = $id;
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute($params);
            $rowCount = $stm->rowCount();
            return $rowCount > 0; // Return true if any rows were affected, false otherwise
        } catch (\PDOException $exception) {
            throw $exception;
        }
    }

    function delete(int $id): bool
    {
        $existingMovie = $this->findById($id);

        if ($existingMovie === null) {
            throw new NotFoundException;
        }
        $sql = "DELETE FROM " . $this->dbTableName . " WHERE uid=?";
        try {
            $stm = $this->getPdo()->prepare($sql);
            $stm->execute([$id]);
        } catch (\PDOException $exception) {
            throw $exception;
        }

        return true;
    }

    function SampleData(Container $container): bool
    {
        try {
            $movieData = [
                [
                    'The Shawshank Redemption',
                    1994,
                    '1994-10-14',
                    '142 min',
                    'Drama',
                    'Frank Darabont',
                    'Tim Robbins, Morgan Freeman',
                    'USA',
                    'https://example.com/poster1.jpg',
                    9.8,
                    'movie',
                ],
                [
                    'The Godfather',
                    1972,
                    '1972-03-24',
                    '175 min',
                    'Crime, Drama',
                    'Francis Ford Coppola',
                    'Marlon Brando, Al Pacino',
                    'USA',
                    'https://example.com/poster2.jpg',
                    10,
                    'movie',
                ],
                [
                    'Pulp Fiction',
                    1994,
                    '1994-10-14',
                    '154 min',
                    'Crime, Drama',
                    'Quentin Tarantino',
                    'John Travolta, Uma Thurman',
                    'USA',
                    'https://example.com/poster3.jpg',
                    5.5,
                    'movie',
                ],
                [
                    'The Dark Knight',
                    2008,
                    '2008-07-18',
                    '152 min',
                    'Action, Crime, Drama',
                    'Christopher Nolan',
                    'Christian Bale, Heath Ledger',
                    'USA',
                    'https://example.com/poster4.jpg',
                    6,
                    'movie'
                ],
                [
                    'Forrest Gump',
                    1994,
                    '1994-07-06',
                    '142 min',
                    'Drama, Romance',
                    'Robert Zemeckis',
                    'Tom Hanks, Robin Wright',
                    'USA',
                    'https://example.com/poster5.jpg',
                    8.8,
                    'movie',
                ],
            ];

            foreach ($movieData as $data) {
                $this->insert($data);
            }
        } catch (Exception $exception) {
            error_log($exception->getMessage());
            return false;
        }

        return true;
    }


}