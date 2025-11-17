<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With");

require_once "../config/database.php";

$db = (new Database())->connect();
$method = $_SERVER['REQUEST_METHOD'];

// Parse input (for POST/PUT)
$input = json_decode(file_get_contents("php://input"), true);

// --- ROUTES BASED ON HTTP METHOD ---
switch($method) {
    case 'GET':
        // GET /users.php           => ambil semua data
        // GET /users.php?id=1      => ambil 1 data
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($data ?: ["message" => "User not found"]);
        } else {
            $stmt = $db->query("SELECT * FROM users ORDER BY id DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($data);
        }
        break;

    case 'POST':
        // POST /users.php (body: username,email,password)
        if (!empty($input['username']) && !empty($input['email']) && !empty($input['password'])) {
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([
                $input['username'],
                $input['email'],
                password_hash($input['password'], PASSWORD_DEFAULT)
            ]);
            echo json_encode(["message" => "User created successfully"]);
        } else {
            echo json_encode(["message" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        // PUT /users.php?id=1 (body: username,email,password)
        if (isset($_GET['id'])) {
            $fields = [];
            $params = [];

            if (!empty($input['username'])) {
                $fields[] = "username = ?";
                $params[] = $input['username'];
            }
            if (!empty($input['email'])) {
                $fields[] = "email = ?";
                $params[] = $input['email'];
            }
            if (!empty($input['password'])) {
                $fields[] = "password = ?";
                $params[] = password_hash($input['password'], PASSWORD_DEFAULT);
            }

            if (count($fields) > 0) {
                $params[] = $_GET['id'];
                $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute($params);
                echo json_encode(["message" => "User updated successfully"]);
            } else {
                echo json_encode(["message" => "No fields to update"]);
            }
        } else {
            echo json_encode(["message" => "User ID is required"]);
        }
        break;

    case 'DELETE':
        // DELETE /users.php?id=1
        if (isset($_GET['id'])) {
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode(["message" => "User deleted successfully"]);
        } else {
            echo json_encode(["message" => "User ID is required"]);
        }
        break;

    default:
        echo json_encode(["message" => "Invalid HTTP Method"]);
        break;
}
?>
