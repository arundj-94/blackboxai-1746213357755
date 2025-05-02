<?php
header('Content-Type: application/json');
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

function getInputData() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

switch ($method) {
    case 'GET':
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY name ASC');
        $categories = $stmt->fetchAll();
        echo json_encode($categories);
        break;

    case 'POST':
        $data = getInputData();
        if (!isset($data['name']) || empty(trim($data['name']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Category name is required']);
            exit;
        }
        $name = trim($data['name']);

        // Check if category already exists
        $stmt = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['error' => 'Category already exists']);
            exit;
        }

        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        $stmt->execute([$name]);
        $id = $pdo->lastInsertId();

        http_response_code(201);
        echo json_encode(['id' => $id, 'name' => $name]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
