<?php

use App\databases\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/voting', function (Request $request, Response $response, $args) {
    $sql = "CALL GetUserHasVoting()";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode(['data' => $customers]));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});
    
$app->get('/voting/{id_voting}', function (Request $request, Response $response, $args) {
    $id_voting = $request->getAttribute('id_voting');
    $sql = "CALL GetUserVotingById($id_voting)";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode(['data' => $customers]));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->post('/voting', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $id_user = $data["id_user"];
    $id_kandidat = $data["id_kandidat"];

    try {
        $db = new DB();
        $conn = $db->connect();

        $queryCheckRole = $conn->prepare('CALL GetUserRoleById(:userId)');
        $queryCheckRole->bindParam(':userId', $id_user);
        $queryCheckRole->execute();
        $userRole = $queryCheckRole->fetchColumn();
        
        if ($userRole === 2) {
            // Jika peran adalah 2 (tidak boleh memilih)
            $data = ['error' => 'Anda tidak memiliki izin untuk memilih, Karena anda Admin.'];
        } else {
            // Periksa peran dan lakukan pemungutan suara jika peran adalah 1 (boleh memilih)
            $query = $conn->prepare('CALL voteforcandidate(:userId, :candidateId)');
            $query->bindParam(':userId', $id_user);
            $query->bindParam(':candidateId', $id_kandidat);

            if ($query->execute()) {
                // Pemungutan suara berhasil
                $data = ['message' => 'Data user : ' . $id_user . ' dan memilih kandidat ' . $id_kandidat];
            } else {
                // Kesalahan dalam eksekusi prosedur
                $data = ['error' => 'Gagal melakukan pemungutan suara.'];
            }
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => 'Hak suara anda telah terpakai >> ' . $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});