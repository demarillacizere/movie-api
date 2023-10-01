<?php

use MovieApi\App\DB;
use MovieApi\Controllers\ExceptionController;
use MovieApi\Middlewares\MiddlewareAfter;
use MovieApi\Middlewares\MiddlewareBefore;
use DI\Container;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);

$app = AppFactory::create();

$container->set('settings', function () {
    $dotenv = Dotenv::createImmutable(__DIR__ . "/..");
    $dotenv->safeLoad();
    return $_ENV;
});

$container->set('database', function () use ($container) {
    $db = new DB($container);
    return $db->connection;
});


$container->set('view', function () {
    return new PhpRenderer(__DIR__ . "/../src/Views");
});

$app->group('/v1', function (RouteCollectorProxy $group) {
    $group->get('/movies', '\MovieApi\Controllers\MoviesController:indexAction');
    $group->post('/movies', '\MovieApi\Controllers\MoviesController:addAction');
    $group->put('/movies/{id:[0-9]+}', '\MovieApi\Controllers\MoviesController:updateAction');
    $group->delete('/movies/{id:[0-9]+}', '\MovieApi\Controllers\MoviesController:deleteAction');
    $group->patch('/movies/{id:[0-9]+}', '\MovieApi\Controllers\MoviesController:patchAction');
    $group->get('/movies/{numberPerPage:[0-9]+}', '\MovieApi\Controllers\MoviesController:numberPerPageAction');
    $group->get('/movies/{numberPerPage:[0-9]+}/sort/{fieldToSort}', '\MovieApi\Controllers\MoviesController:sortedNumberPerPageAction');
    $group->get('/movies/fill-with-sample-data', '\MovieApi\Controllers\MoviesController:fakeAction');
    $group->get('/apidocs', '\MovieApi\Controllers\OpenAPIController:documentationAction');
})->add(new MiddlewareBefore($container))->add(new MiddlewareAfter($container));

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (Psr\Http\Message\ServerRequestInterface $request) use ($container) {
        $controller = new ExceptionController($container);
        return $controller->notFound($request, new Response());
    }
);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpMethodNotAllowedException::class,
    function (Psr\Http\Message\ServerRequestInterface $request) use ($container) {
        $controller = new ExceptionController($container);
        return $controller->methodNotAllowed($request, new Response());
    }
);

$app->run();