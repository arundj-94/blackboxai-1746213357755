<?php
require_once 'db.php';

function getNextOccurrence($task) {
    $today = new DateTime();
    $next = $task['next_occurrence'] ? new DateTime($task['next_occurrence']) : null;
    if (!$next || $next > $today) {
        return null;
    }

    $repetition = $task['repetition'];
    $details = json_decode($task['repetition_details'], true);

    switch ($repetition) {
        case 'Daily':
            $next->modify('+1 day');
            break;

        case 'Weekly':
            if (!isset($details['days']) || !is_array($details['days']) || count($details['days']) === 0) {
                // Default to next week same day
                $next->modify('+7 days');
                break;
            }
            $daysOfWeek = ['Sun'=>0, 'Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6];
            $currentDay = (int)$next->format('w'); // 0 (Sun) to 6 (Sat)
            $sortedDays = array_map(function($d) use ($daysOfWeek) { return $daysOfWeek[$d]; }, $details['days']);
            sort($sortedDays);

            // Find next day in the list after currentDay
            $nextDay = null;
            foreach ($sortedDays as $d) {
                if ($d > $currentDay) {
                    $nextDay = $d;
                    break;
                }
            }
            if ($nextDay === null) {
                // Wrap to first day next week
                $nextDay = $sortedDays[0];
                $next->modify('+7 days');
            }
            $diff = ($nextDay - $currentDay + 7) % 7;
            if ($diff === 0) $diff = 7;
            $next->modify("+$diff days");
            break;

        case 'Monthly':
            if (!isset($details['day'])) {
                $next->modify('+1 month');
                break;
            }
            $day = (int)$details['day'];
            $next->modify('first day of next month');
            $daysInMonth = (int)$next->format('t');
            if ($day > $daysInMonth) {
                $day = $daysInMonth;
            }
            $next->setDate((int)$next->format('Y'), (int)$next->format('m'), $day);
            break;

        case 'Yearly':
            if (!isset($details['month']) || !isset($details['day'])) {
                $next->modify('+1 year');
                break;
            }
            $month = (int)$details['month'];
            $day = (int)$details['day'];
            $year = (int)$next->format('Y') + 1;
            $next->setDate($year, $month, $day);
            break;

        default:
            return null;
    }

    return $next->format('Y-m-d');
}

try {
    $todayStr = (new DateTime())->format('Y-m-d');
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE repetition != 'None' AND next_occurrence IS NOT NULL AND next_occurrence <= ?");
    $stmt->execute([$todayStr]);
    $tasks = $stmt->fetchAll();

    foreach ($tasks as $task) {
        // Create new task for this occurrence
        $insertStmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, priority, status, category_id, repetition, repetition_details, next_occurrence) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([
            $task['title'],
            $task['description'],
            $task['next_occurrence'],
            $task['priority'],
            'Pending',
            $task['category_id'],
            $task['repetition'],
            $task['repetition_details'],
            null
        ]);

        // Update next_occurrence of original task
        $newNext = getNextOccurrence($task);
        $updateStmt = $pdo->prepare("UPDATE tasks SET next_occurrence = ? WHERE id = ?");
        $updateStmt->execute([$newNext, $task['id']]);
    }

    echo "Repeat tasks processed successfully.\n";
} catch (Exception $e) {
    echo "Error processing repeat tasks: " . $e->getMessage() . "\n";
}
?>
