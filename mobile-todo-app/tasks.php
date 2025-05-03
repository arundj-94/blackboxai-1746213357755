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
        $stmt = $pdo->query('SELECT t.*, c.name AS category_name FROM tasks t JOIN categories c ON t.category_id = c.id ORDER BY t.order_index ASC');
        $tasks = $stmt->fetchAll();
        echo json_encode($tasks);
        break;

    case 'POST':
        $data = getInputData();
        if (!isset($data['title']) || empty(trim($data['title']))) {
            http_response_code(400);
            echo json_encode(['error' => 'Title is required']);
            exit;
        }
        $title = trim($data['title']);
        $description = isset($data['description']) ? trim($data['description']) : null;
        $start_date = isset($data['start_date']) ? $data['start_date'] : date('Y-m-d');
        $due_date = isset($data['due_date']) ? $data['due_date'] : date('Y-m-d');
        $priority = isset($data['priority']) ? $data['priority'] : 'Medium';
        $status = 'Pending';
        $category_id = isset($data['category_id']) ? $data['category_id'] : null;
        $repetition = isset($data['repetition']) ? $data['repetition'] : 'None';
        $repetition_details = isset($data['repetition_details']) ? $data['repetition_details'] : null;

        if (!$category_id) {
            // Get default category id for 'tasks'
            $stmtCat = $pdo->prepare('SELECT id FROM categories WHERE name = ?');
            $stmtCat->execute(['tasks']);
            $category_id = $stmtCat->fetchColumn();
            if (!$category_id) {
                http_response_code(500);
                echo json_encode(['error' => 'Default category "tasks" not found']);
                exit;
            }
        }

        $maxOrderStmt = $pdo->query('SELECT COALESCE(MAX(order_index), 0) FROM tasks');
        $maxOrder = $maxOrderStmt->fetchColumn();
        $order_index = $maxOrder + 1;

        $stmt = $pdo->prepare('INSERT INTO tasks (title, description, start_date, due_date, priority, status, category_id, order_index) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $description, $start_date, $due_date, $priority, $status, $category_id, $order_index]);
        $id = $pdo->lastInsertId();

        if ($repetition !== 'None') {
            $repeatStmt = $pdo->prepare('INSERT INTO repeated_tasks (task_id, repetition, repetition_details, last_added_date) VALUES (?, ?, ?, NULL)');
            $repeatStmt->execute([$id, $repetition, $repetition_details]);
        }

        http_response_code(201);
        echo json_encode(['id' => $id]);
        break;

    case 'PUT':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task ID is required']);
            exit;
        }
        $id = (int)$_GET['id'];
        $data = getInputData();

        $fields = [];
        $values = [];

        $allowedFields = ['title', 'description', 'start_date', 'due_date', 'priority', 'status', 'category_id', 'order_index'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['error' => 'No valid fields to update']);
            exit;
        }

        $values[] = $id;
        $sql = 'UPDATE tasks SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        http_response_code(200);
        echo json_encode(['message' => 'Task updated']);
        break;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Task ID is required']);
            exit;
        }
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
        $stmt->execute([$id]);
        http_response_code(200);
        echo json_encode(['message' => 'Task deleted']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
