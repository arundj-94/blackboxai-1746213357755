<?php
require_once 'db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

function getInputData() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

switch ($method) {
    case 'GET':
        $stmt = $pdo->query('SELECT * FROM categories ORDER BY name');
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
        $stmt = $pdo->prepare('INSERT INTO categories (name) VALUES (?)');
        try {
            $stmt->execute([$name]);
            http_response_code(201);
            echo json_encode(['id' => $pdo->lastInsertId(), 'name' => $name]);
        } catch (PDOException $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Category already exists']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
