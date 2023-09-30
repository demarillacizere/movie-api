<?php

namespace MovieApi\Controllers;

use Assert\AssertionFailedException;
use MovieApi\Models\Content;
use MovieApi\Models\Image;
use MovieApi\Models\Movies;
use MovieApi\Models\Title;
use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use MovieApi\Models\RequestValidator;

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
        $movies = new Movies($this->container);
        $data = $movies->findAll();
        return $this->render($data, $response);
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
     *                      property="authorId",
     *                      description="ID of author of new movie",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="img",
     *                      description="Image URL of new movie",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      description="Content of new movie",
     *                      type="string",
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
            // Use the RequestValidator class to validate and sanitize the data
            $validatedData = RequestValidator::PostAndPutValidation($requestBody);
    
            // If all assertions pass and data is sanitized, proceed to use it
            // For example, you can pass it to your model or database operation
            $movie = new Movies($this->container);
            $movie->insert([
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
        } catch (\InvalidArgumentException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_BAD_REQUEST);
            return $this->render($responseData, $response);
        }

        $responseData = [
            'code' => StatusCodeInterface::STATUS_OK,
            'message' => 'Movie has been added'
        ];

        return $this->render($responseData, $response);
    }

    /**
     * @OA\Put(
     *     path="/v1/posts/{id}",
     *     description="update a single movie from blog based on movie ID",
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
     *           description="movie has been created successfully",
     *       ),
     * @OA\Response(
     *           response=400,
     *           description="bad request",
     *       ),
     *     @OA\Response(
     *                response=404,
     *            description="Post not found",
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
            $movies->update(
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
                    $id
                ]
            );
        } catch (AssertionFailedException $e) {
            $responseData = [
                'code' => StatusCodeInterface::STATUS_BAD_REQUEST,
                'message' => $e->getMessage()
            ];
            $response = new JsonResponse($responseData, StatusCodeInterface::STATUS_BAD_REQUEST);
            return $this->render($responseData, $response);
        }

        $responseData = [
            'code' => StatusCodeInterface::STATUS_OK,
            'message' => 'Movie has been updated.'
        ];

        return $this->render($responseData, $response);
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
        $movies = new Movies($this->container);
        $movies->delete($id);
        $responseData = [
            'code' => StatusCodeInterface::STATUS_OK,
            'message' => 'Movie has been deleted.'
        ];
        return $this->render($responseData, $response);
    }

    public function patchAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $id = $args['id'];
        $requestBody = json_decode($request->getBody(), true);

        try {
            $validatedData = RequestValidator::ValidateAndSanitizeFields($requestBody);
            $movie = new Movies($this->container);
            $updatedMovie = $movie->patch($id, $validatedData);
            $response->getBody()->write(json_encode(['message' => 'Movie updated successfully', 'data' => $updatedMovie]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\PDOException $exception) {
            // Handle database errors and return an error response
            $response->getBody()->write(json_encode(['error' => $exception->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
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
        $movies = new Movies($this->container);
        $data = $movies->findByNumberPerPageAndSort($numberOfMovies, $fieldToSort);
        return $this->render($data, $response);
    }

    public function fakeAction(Request $request, Response $response, $args = []): ResponseInterface
    {
        $movies = new Movies($this->container);
        $movies->SampleData($this->container);

        $responseData = [
            'code' => StatusCodeInterface::STATUS_OK,
            'message' => 'fake data has been inserted'
        ];
        return $this->render($responseData, $response);
    }
}