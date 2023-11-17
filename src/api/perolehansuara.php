<?php

use App\databases\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/perolehansuara', function (Request $request, Response $response) {
    try {
        $db = new DB();
        $conn = $db->connect();
        $queryGetData = $conn->prepare('SELECT * FROM perolehansuara');
        $queryGetData->execute();
        
        $data = $queryGetData->fetchAll(PDO::FETCH_ASSOC);

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal mendapatkan data dari view perolehansuara >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});
