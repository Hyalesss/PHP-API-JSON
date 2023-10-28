<?php

use App\databases\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/user', function (Request $request, Response $response, $args) {
    $sql = "CALL GetAllUser()";

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
    

    $app->get('/user/{id_user}', function (Request $request, Response $response, $args) {
        $id_user = $request->getAttribute('id_user');
        $sql = "CALL GetUsers($id_user)";
    
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


    $app->post('/user', function (Request $request, Response $response, array $args) {
        $data = $request->getParsedBody();
        $id = $data["id_roles"];
        $nama = $data["nama"];
        $angkatan = $data["angkatan"];
    
        $sql = "CALL adduser(:id_role, :nama , :angkatan)";
    
        try {
            $db = new DB();
            $conn = $db->connect();
    
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_role', $id);
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':angkatan', $angkatan);
        
            $lastId = null;

            if ($stmt->execute()) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $lastId = $result['new_id'];
                }
            }   

            $db = null;
            $response->getBody()->write(json_encode( [
                'message' => 'Data user disimpan dengan id ' . $lastId
            ]));
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

    $app->put('/user/{id_user}', function (Request $request, Response $response, array $args) {
        $id_user = $request->getAttribute('id_user');
    
        $data = $request->getParsedBody();
        $nama = $data["nama"];
        $angkatan = $data["angkatan"];
        $id_user = $args["id_user"];
    
        $sql = 'CALL UpdateUserr(:no_id_User, :namas, :angkatannn)';
    
        try {
            $db = new DB();
            $conn = $db->connect();
    
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':no_id_User', $id_user, PDO::PARAM_INT);
            $stmt->bindParam(':namas', $nama, PDO::PARAM_STR);
            $stmt->bindParam(':angkatannn', $angkatan, PDO::PARAM_INT);
    
            $result = $stmt->execute();
    
            $db = null;
            echo "Update successful! ";
            $response->getBody()->write(json_encode($result));
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

    $app->delete('/user/{id_user}', function (Request $request, Response $response, array $args) {
        $id_user = $request->getAttribute('id_user');
    
        $sql = "CALL deleteuserwithlog($id_user)";
    
        try {
            $db = new DB();
            $conn = $db->connect();
    
            $db = null;
            $response->getBody()->write(json_encode(['message' => 'Pengguna dengan ID ' . $id_user . ' dihapus dari database']));
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