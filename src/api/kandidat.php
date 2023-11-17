<?php

use App\databases\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/kandidat', function (Request $request, Response $response, $args) {
    $sql = "CALL GetKandidatOpsi()";

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
    
$app->get('/kandidat/{id_kandidat}', function (Request $request, Response $response, $args) {
    $id_kandidat = $request->getAttribute('id_kandidat');
    $sql = "CALL GetKandidat($id_kandidat)";

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

$app->post('/kandidat', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $nama = $data["nama"];
    $visi_misi = $data["visi_misi"];
    $foto = $data["foto"];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk menambahkan kandidat baru
        $queryTambahKandidat = $conn->prepare('CALL TambahKandidat(:nama_kandidat, :visidanmisi, :foto_path)');
        $queryTambahKandidat->bindParam(':nama_kandidat', $nama);
        $queryTambahKandidat->bindParam(':visidanmisi', $visi_misi);
        $queryTambahKandidat->bindParam(':foto_path', $foto);

        if ($queryTambahKandidat->execute()) {
            $data = ['message' => 'Kandidat berhasil ditambahkan.'];
        } else {
            $data = ['error' => 'Gagal menambahkan kandidat.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal menambahkan kandidat >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->put('/kandidat/{id_kandidat}', function (Request $request, Response $response, array $args) {
    $id_kandidat = $args['id_kandidat'];
    $data = $request->getParsedBody();
    $nama = $data["nama"];
    $visi_misi = $data["visi_misi"];
    $foto = $data["foto"];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk mengupdate kandidat
        $queryUpdateKandidat = $conn->prepare('CALL UpdateKandidat(:id_kandidat_to_update, :nama_kandidat, :visidanmisi, :foto_path)');
        $queryUpdateKandidat->bindParam(':id_kandidat_to_update', $id_kandidat);
        $queryUpdateKandidat->bindParam(':nama_kandidat', $nama);
        $queryUpdateKandidat->bindParam(':visidanmisi', $visi_misi);
        $queryUpdateKandidat->bindParam(':foto_path', $foto);

        if ($queryUpdateKandidat->execute()) {
            $data = ['message' => 'Kandidat berhasil diupdate.'];
        } else {
            $data = ['error' => 'Gagal mengupdate kandidat.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal mengupdate kandidat >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


$app->delete('/kandidat/{id_kandidat}', function (Request $request, Response $response, array $args) {
    $id_kandidat = $args['id_kandidat'];

    try {
        $db = new DB();
        $conn = $db->connect();

        // Panggil prosedur untuk menghapus kandidat
        $queryHapusKandidat = $conn->prepare('CALL HapusKandidat(:id_kandidat)');
        $queryHapusKandidat->bindParam(':id_kandidat', $id_kandidat);

        if ($queryHapusKandidat->execute()) {
            $data = ['message' => 'Kandidat berhasil dihapus.'];
        } else {
            $data = ['error' => 'Gagal menghapus kandidat.'];
        }

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => 'Gagal menghapus kandidat >> ' . $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});
