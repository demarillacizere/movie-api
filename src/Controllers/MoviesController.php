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

/**
 * @OA\Info(
 *   title="Blog API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="hennadii.shvedko@jagaad.com"
 *   )
 * )
 */
class MoviesController extends A_Controller
{
    /**
     * @OA\Get(
     *     path="/v1/posts",
     *     description="Returns all posts",
     *     @OA\Response(
     *          response=200,
     *          description="posts response",
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
     *     path="/v1/posts",
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
     *                      description="ID of author of new post",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="img",
     *                      description="Image URL of new post",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      description="Content of new post",
     *                      type="string",
     *                  ),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="post has been created successfully",
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
        $title = filter_var($requestBody['title'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $year = filter_var($requestBody['year'], FILTER_SANITIZE_NUMBER_INT);
        $released = filter_var($requestBody['released'], FILTER_SANITIZE_STRING);
        $runtime = filter_var($requestBody['runtime'], FILTER_SANITIZE_STRING);
        $genre = filter_var($requestBody['genre'], FILTER_SANITIZE_STRING);
        $director = filter_var($requestBody['director'], FILTER_SANITIZE_STRING);
        $actors = filter_var($requestBody['actors'], FILTER_SANITIZE_STRING);
        $country = filter_var($requestBody['country'], FILTER_SANITIZE_STRING);
        $poster = filter_var($requestBody['poster'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $imdb = filter_var($requestBody['imdb'], FILTER_SANITIZE_NUMBER_FLOAT);
        $type = filter_var($requestBody['type'], FILTER_SANITIZE_STRING);


        $movies = new Movies($this->container);
        try {
            $movies->insert(
                [
                    $title,
                    $year,
                    $released,
                    $runtime,
                    $genre,
                    $director,
                    $actors,
                    $country,
                    $poster,
                    $imdb,
                    $type
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
            'message' => 'Movie has been added'
        ];

        return $this->render($responseData, $response);
    }

    /**
     * @OA\Put(
     *     path="/v1/posts/{id}",
     *     description="update a single post from blog based on post ID",
     *     @OA\Parameter(
     *          description="ID of post to update",
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
     *                       description="title of new post",
     *                       type="string",
     *                   ),
     *                   @OA\Property(
     *                       property="authorId",
     *                       description="ID of author of new post",
     *                       type="integer",
     *                   ),
     *                   @OA\Property(
     *                       property="img",
     *                       description="Image URL of new post",
     *                       type="string",
     *                   ),
     *                   @OA\Property(
     *                       property="content",
     *                       description="Content of new post",
     *                       type="string",
     *                   ),
     *               ),
     *           ),
     *       ),
     * @OA\Response(
     *           response=200,
     *           description="post has been created successfully",
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
        $requestBody = $this->getRequestBodyAsArray($request);

        $id = $args['id'];
        $title = filter_var($requestBody['title'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $year = filter_var($requestBody['year'], FILTER_SANITIZE_NUMBER_INT);
        $released = filter_var($requestBody['released'], FILTER_SANITIZE_NUMBER_INT);
        $runtime = filter_var($requestBody['runtime'], FILTER_SANITIZE_NUMBER_INT);
        $genre = filter_var($requestBody['genre'], FILTER_SANITIZE_NUMBER_INT);
        $director = filter_var($requestBody['director'], FILTER_SANITIZE_NUMBER_INT);
        $actors = filter_var($requestBody['actors'], FILTER_SANITIZE_NUMBER_INT);
        $country = filter_var($requestBody['country'], FILTER_SANITIZE_NUMBER_INT);
        $poster = filter_var($requestBody['poster'], FILTER_SANITIZE_STRING | FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $imdb = filter_var($requestBody['imdb'], FILTER_SANITIZE_NUMBER_INT);
        $type = filter_var($requestBody['type'], FILTER_SANITIZE_NUMBER_INT);
        

        $movies = new Movies($this->container);
        try {
            $movies->update(
                [
                    $title,
                    $year,
                    $released,
                    $runtime,
                    $genre,
                    $director,
                    $actors,
                    $country,
                    $poster,
                    $imdb,
                    $type,
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
     *     description="deletes a single post from blog based on pot ID",
     *     @OA\Parameter(
     *         description="ID of post to delete",
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
     *         description="post has been deleted"
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