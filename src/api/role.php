<?php

use App\databases\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/role/{id}', function (Request $request, Response $response, array $args) {
    $id_role = $args['id'];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk mendapatkan role
        $queryGetRole = $conn->prepare('CALL GetRole(:id_role_to_get)');
        $queryGetRole->bindParam(':id_role_to_get', $id_role);
        $queryGetRole->execute();

        $role = $queryGetRole->fetch(PDO::FETCH_ASSOC);

        if ($role) {
            $data = ['role' => $role];
        } else {
            $data = ['error' => 'Role tidak ditemukan.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal mendapatkan role >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});



$app->post('/role', function (Request $request, Response $response) {
    $data = $request->getParsedBody();
    $nama_role = $data["nama_role"];
    $status_kelayakan = $data["status_kelayakan"];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk menambahkan role baru
        $queryTambahRole = $conn->prepare('CALL TambahRole(:penambahan_nama_role, :penambahan_status_kelayakan)');
        $queryTambahRole->bindParam(':penambahan_nama_role', $nama_role);
        $queryTambahRole->bindParam(':penambahan_status_kelayakan', $status_kelayakan);

        if ($queryTambahRole->execute()) {
            $data = ['message' => 'Role berhasil ditambahkan.'];
        } else {
            $data = ['error' => 'Gagal menambahkan role.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal menambahkan role >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->put('/role/{id_role}', function (Request $request, Response $response, array $args) {
    $id_role = $args['id_role'];
    $data = $request->getParsedBody();
    $nama_role = $data["nama_role"];
    $status_kelayakan = $data["status_kelayakan"];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk mengupdate role
        $queryUpdateRole = $conn->prepare('CALL UpdateRole(:id_role_to_update, :pengupdate_role, :pengupdate_status_kelayakan)');
        $queryUpdateRole->bindParam(':id_role_to_update', $id_role);
        $queryUpdateRole->bindParam(':pengupdate_role', $nama_role);
        $queryUpdateRole->bindParam(':pengupdate_status_kelayakan', $status_kelayakan);

        if ($queryUpdateRole->execute()) {
            $data = ['message' => 'Role berhasil diupdate.'];
        } else {
            $data = ['error' => 'Gagal mengupdate role.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal mengupdate role >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->delete('/role/{id_role}', function (Request $request, Response $response, array $args) {
    $id_role = $args['id_role'];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk menghapus role
        $queryHapusRole = $conn->prepare('CALL HapusRole(:id_role_to_delete)');
        $queryHapusRole->bindParam(':id_role_to_delete', $id_role);

        if ($queryHapusRole->execute()) {
            $data = ['message' => 'Role berhasil dihapus.'];
        } else {
            $data = ['error' => 'Gagal menghapus role.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal menghapus role >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});