<?php

namespace MovieApi\Controllers;

use Exception;
use MovieApi\Models\Movies;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use MovieApi\Models\RequestValidator;
use DI\NotFoundException;

/**
 * @OA\Info(
 *   title="Movie API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="izered3@gmail.com"
 *   )
 * )
 */
class MoviesController extends A_Controller
{
    /**
     * @OA\Get(
     *     path="/v1/movies",
     *     description="Returns all movies",
     *     @OA\Response(
     *          response=200,
     *          description="movies response",
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *   )
     * )
     * @return \Laminas\Diactoros\Response
     */
    public function indexAction(Request $request, Response $response): ResponseInterface
    {
        try {
            $movies = new Movies($this->container);
            $data = $movies->findAll();
            return $this->render($data, $response);
        } catch (Exception $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            return $this->render($responseData, $response);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/movies",
     *     description="Add a movie",
     *     @OA\RequestBody(
     *          description="Input data format",
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="title",
     *                      description="title of the movie",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="year",
     *                      description="Release year for the movie",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="release",
     *                      description="Date of release of the movie",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="poster",
     *                      description="Image URL of the movie poster",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="imdb",
     *                      description="Imdb rating of the movie",
     *                      type="float",
     *                  ),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="movie has been created successfully",
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="bad request",
     *      ),
     *      @OA\Response(
     *            response=500,
     *            description="Internal server error",
     *        ),
     *   ),
     * )
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function addAction(Request $request, Response $response): ResponseInterface
    {
        $requestBody = json_decode($request->getBody(), true);
        try {
            $validatedData = RequestValidator::PostAndPutValidation($requestBody);
            $movie = new Movies($this->container);
            $movie->insert(
                [
                    $validatedData['title'],
                    $validatedData['year'],
                    $validatedData['released'],
                    $validatedData['runtime'],
                    $validatedData['genre'],
                    $validatedData['director'],
                    $validatedData['actors'],
                    $validatedData['country'],
                    $validatedData['poster'],
                    $validatedData['imdb'],
                    $validatedData['type'],
                ]

            );
            $responseData = [
                'code' => StatusCodeInterface::STATUS_OK,
                'message' => 'Movie has been added'
            ];
    
            return $this->render($responseData, $response);
        } catch (\InvalidArgumentException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_BAD_REQUEST);
            return $this->render($responseData, $response);
        } catch (Exception $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            return $this->render($responseData, $response);
        }

        
    }

    /**
     * @OA\Put(
     *     path="/v1/movies/{id}",
     *     description="update a single movie based on movie ID",
     *     @OA\Parameter(
     *          description="ID of movie to update",
     *          in="path",
     *          name="id",
     *          required=true,
     *          @OA\Schema(
     *              format="int64",
     *              type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *           description="Input data format",
     *           @OA\MediaType(
     *               mediaType="multipart/form-data",
     *               @OA\Schema(
     *                   type="object",
     *                   @OA\Property(
     *                       property="title",
     *                       description="title of new movie",
     *                       type="string",
     *                   ),
     *                   @OA\Property(
     *                       property="authorId",
     *                       description="ID of author of new movie",
     *                       type="integer",
     *                   ),
     *                   @OA\Property(
     *                       property="img",
     *                       description="Image URL of new movie",
     *                       type="string",
     *                   ),
     *                   @OA\Property(
     *                       property="content",
     *                       description="Content of new movie",
     *                       type="string",
     *                   ),
     *               ),
     *           ),
     *       ),
     * @OA\Response(
     *           response=200,
     *           description="Movie has been added successfully",
     *       ),
     * @OA\Response(
     *           response=400,
     *           description="bad request",
     *       ),
     *     @OA\Response(
     *                response=404,
     *            description="Movie not found",
     *        ),
     *     @OA\Response(
     *            response=500,
     *            description="Internal server error",
     *        ),
     *  )
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return ResponseInterface
     */
    public function updateAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $requestBody = json_decode($request->getBody(), true);
        $id = $args['id'];
        $movies = new Movies($this->container);
        try {
            $validatedData = RequestValidator::PostAndPutValidation($requestBody);
            $movieUpdate = $movies->update(
                $id,
                [
                    $validatedData['title'],
                    $validatedData['year'],
                    $validatedData['released'],
                    $validatedData['runtime'],
                    $validatedData['genre'],
                    $validatedData['director'],
                    $validatedData['actors'],
                    $validatedData['country'],
                    $validatedData['poster'],
                    $validatedData['imdb'],
                    $validatedData['type']
                ]
            );
            if ($movieUpdate) {
                $responseData = [
                    'code' => StatusCodeInterface::STATUS_OK,
                    'message' => 'Movie has been updated.'
                ];
                return $this->render($responseData, $response);
            } else {
                throw new Exception("Something went wrong. Movie not updated.");
            }
        } catch (\InvalidArgumentException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_BAD_REQUEST);
            return $this->render($responseData, $response);
        } catch (NotFoundException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_NOT_FOUND,
                'message' => "Movie with id $id is not found"
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_NOT_FOUND);
            return $this->render($responseData, $response);
        } catch (Exception $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            return $this->render($responseData, $response);
        }
    }

    /**
     * @OA\Delete(
     *     path="/v1/posts/{id}",
     *     description="deletes a single movie from blog based on pot ID",
     *     @OA\Parameter(
     *         description="ID of movie to delete",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             format="int64",
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="movie has been deleted"
     *     ),
     * @OA\Response(
     *            response=400,
     *            description="bad request",
     *        ),
     * @OA\Response(
     *                 response=404,
     *             description="Post not found",
     *         ),
     * @OA\Response(
     *             response=500,
     *             description="Internal server error",
     *         ),
     *   )
     */
    public function deleteAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $id = $args['id'];
        try {
            $movies = new Movies($this->container);
            $movies->delete($id);
            $responseData = [
                'code' => StatusCodeInterface::STATUS_OK,
                'message' => 'Movie has been deleted.'
            ];
            return $this->render($responseData, $response);
        } catch (NotFoundException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_NOT_FOUND,
                'message' => "Movie with id $id is not found"
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_NOT_FOUND);
            return $this->render($responseData, $response);
        } catch (Exception $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            return $this->render($responseData, $response);
        }
    }

    public function patchAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $id = $args['id'];
        $requestBody = json_decode($request->getBody(), true);

        try {
            $validatedData = RequestValidator::ValidateAndSanitizeFields($requestBody);
            $movie = new Movies($this->container);
            $movieUpdate = $movie->patch($id, $validatedData);
            if ($movieUpdate) {
                $responseData = [
                    'code' => StatusCodeInterface::STATUS_OK,
                    'message' => 'Movie has been updated.'
                ];
                return $this->render($responseData, $response);
            } else {
                throw new Exception("Something went wrong.");
            }
        } catch (\InvalidArgumentException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_BAD_REQUEST);
            return $this->render($responseData, $response);
        } catch (NotFoundException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_NOT_FOUND,
                'message' => "Movie with id $id is not found"
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_NOT_FOUND);
            return $this->render($responseData, $response);
        } catch (Exception $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
            return $this->render($responseData, $response);
        }
    }

    public function numberPerPageAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $numberOfMovies = $args['numberPerPage'];
        $movies = new Movies($this->container);
        $data = $movies->findByNumberPerPage($numberOfMovies);
        return $this->render($data, $response);
    }

    public function sortedNumberPerPageAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $numberOfMovies = $args['numberPerPage'];
        $fieldToSort = $args['fieldToSort'];
        $allowedFields = ['uid', 'title', 'year', 'released', 'genre', 'type', 'imdb', 'actors', 'director', 'country', 'poster', 'runtime'];
        if (!in_array($fieldToSort, $allowedFields)) {
            $responseStatus = [
                'status' => StatusCodeInterface::STATUS_BAD_REQUEST,
                'message' => 'Invalid field to sort'
            ];
            return $this->render($responseStatus, $response);
        }

        $movies = new Movies($this->container);
        $data = $movies->findByNumberPerPageAndSort($numberOfMovies, $fieldToSort);
        return $this->render($data, $response);
    }

    public function fakeAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        try {
            $movies = new Movies($this->container);
            $movies->SampleData($this->container);
            $responseData = [
                'code' => StatusCodeInterface::STATUS_OK,
                'message' => 'Fake data has been inserted'
            ];
            return $this->render($responseData, $response);
        } catch (Exception $exception) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred: ' . $exception->getMessage()
            ];
            return $this->render($responseData, $response);
        }
    }
}