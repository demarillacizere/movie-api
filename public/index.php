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

$app->run();



//$app->group('/users/{id}', function (RouteCollectorProxy $group) {
//    $group->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, array $args) {
//        // Find, delete, patch or replace user identified by $args['id']
//        return $response;
//    })->setName('user');
//    $group->get('/reset-password', function ($request, $response, array $args) {
//        // Route for /users/{id}/reset-password
//        // Reset the password for user identified by $args['id']
//        return $response;
//    })->setName('user-password-reset');
//    $group->get('/profile', function ($request, $response, array $args) {
//        // Route for /users/{id}/profile
//        // Reset the password for user identified by $args['id']
//        return $response;
//    })->setName('user-profile');
//});


//$routeParser = $app->getRouteCollector()->getRouteParser();
//echo $routeParser->urlFor('test-name', ['name' => 'Josh'], ['example' => 'name']);


//
//$app->get('/v1/routing-name-test[/{params:.*}]', function(Request $request, Response $response, $args = []){
//    $params = explode("/", $args['params']);
//    $response->getBody()->write(print_r($params, true));
//    return $response;
//})->setName('test-name');


//$app->get('/v1/routing-name-test/{id:[0-9]+}', function(Request $request, Response $response, $args = []){
//    $response->getBody()->write("ID is - " . $args['id']);
//    return $response;
//})->add(new MiddlewareBefore())->add(new MiddlewareAfter());