<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

include_once "../src/api/user.php";
include_once "../src/api/voting.php";
include_once "../src/api/kandidat.php";
include_once "../src/api/role.php";
include_once "../src/api/perolehansuara.php";

$app->run();