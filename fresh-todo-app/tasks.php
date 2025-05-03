<?php
header('Content-Type: application/json');
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

function getInputData() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}

function getFilteredTasks($pdo, $filters) {
    $sql = 'SELECT tasks.*, categories.name AS category_name FROM tasks LEFT JOIN categories ON tasks.category_id = categories.id';
    $conditions = [];
    $params = [];

    if (isset($filters['status'])) {
        $conditions[] = 'tasks.status = ?';
        $params[] = $filters['status'];
    }

    if (isset($filters['category_id'])) {
        $conditions[] = 'tasks.category_id = ?';
        $params[] = $filters['category_id'];
    }

    if (isset($filters['today']) && $filters['today']) {
        $conditions[] = 'tasks.due_date = CURDATE()';
    }

    if (isset($filters['overdue']) && $filters['overdue']) {
        $conditions[] = 'tasks.due_date < CURDATE() AND tasks.status = "Pending"';
    }

    if (isset($filters['completed_today']) && $filters['completed_today']) {
        $conditions[] = 'tasks.status = "Completed" AND tasks.due_date = CURDATE()';
    }

    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY tasks.order_index ASC, tasks.due_date IS NULL, tasks.due_date ASC, FIELD(tasks.priority, "High", "Medium", "Low"), tasks.created_at ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

switch ($method) {
    case 'GET':
        $filters = [];
        if (isset($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (isset($_GET['category_id'])) {
            $filters['category_id'] = (int)$_GET['category_id'];
        }
        if (isset($_GET['today'])) {
            $filters['today'] = $_GET['today'] === '1' || $_GET['today'] === 'true';
        }
        if (isset($_GET['overdue'])) {
            $filters['overdue'] = $_GET['overdue'] === '1' || $_GET['overdue'] === 'true';
        }
        if (isset($_GET['completed_today'])) {
            $filters['completed_today'] = $_GET['completed_today'] === '1' || $_GET['completed_today'] === 'true';
        }

        $tasks = getFilteredTasks($pdo, $filters);
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
        $due_date = isset($data['due_date']) ? $data['due_date'] : null;
        $priority = isset($data['priority']) ? $data['priority'] : 'Medium';
        $status = 'Pending';
        $category_id = isset($data['category_id']) ? $data['category_id'] : null;
        $repetition = isset($data['repetition']) ? $data['repetition'] : 'None';
        $repetition_details = isset($data['repetition_details']) ? $data['repetition_details'] : null;

        $stmt = $pdo->prepare('INSERT INTO tasks (title, description, due_date, priority, status, category_id, order_index) VALUES (?, ?, ?, ?, ?, ?, ?)');
        // Set order_index to max + 1
        $maxOrderStmt = $pdo->query('SELECT COALESCE(MAX(order_index), 0) FROM tasks');
        $maxOrder = $maxOrderStmt->fetchColumn();
        $order_index = $maxOrder + 1;

        $stmt->execute([$title, $description, $due_date, $priority, $status, $category_id, $order_index]);
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

        if (isset($data['title'])) {
            $fields[] = 'title = ?';
            $values[] = trim($data['title']);
        }
        if (isset($data['description'])) {
            $fields[] = 'description = ?';
            $values[] = trim($data['description']);
        }
        if (isset($data['due_date'])) {
            $fields[] = 'due_date = ?';
            $values[] = $data['due_date'];
        }
        if (isset($data['priority'])) {
            $fields[] = 'priority = ?';
            $values[] = $data['priority'];
        }
        if (isset($data['status'])) {
            $fields[] = 'status = ?';
            $values[] = $data['status'];
        }
        if (isset($data['category_id'])) {
            $fields[] = 'category_id = ?';
            $values[] = $data['category_id'];
        }
        if (isset($data['order_index'])) {
            $fields[] = 'order_index = ?';
            $values[] = $data['order_index'];
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(['error' => 'No fields to update']);
            exit;
        }

        $values[] = $id;
        $sql = 'UPDATE tasks SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

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

        echo json_encode(['message' => 'Task deleted']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>
