<?php
require_once 'db.php';

function getNextOccurrence($repetition, $details, $lastDate) {
    $today = new DateTime();
    $next = $lastDate ? new DateTime($lastDate) : $today;

    switch ($repetition) {
        case 'Daily':
            $next->modify('+1 day');
            break;

        case 'Weekly':
            if (!isset($details['days']) || !is_array($details['days']) || count($details['days']) === 0) {
                $next->modify('+7 days');
                break;
            }
            $daysOfWeek = ['Sun'=>0, 'Mon'=>1, 'Tue'=>2, 'Wed'=>3, 'Thu'=>4, 'Fri'=>5, 'Sat'=>6];
            $currentDay = (int)$next->format('w');
            $sortedDays = array_map(function($d) use ($daysOfWeek) { return $daysOfWeek[$d]; }, $details['days']);
            sort($sortedDays);

            $nextDay = null;
            foreach ($sortedDays as $d) {
                if ($d > $currentDay) {
                    $nextDay = $d;
                    break;
                }
            }
            if ($nextDay === null) {
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
    $stmt = $pdo->prepare("SELECT rt.*, t.title, t.description, t.priority, t.category_id, t.due_date AS original_due_date FROM repeated_tasks rt JOIN tasks t ON rt.task_id = t.id WHERE rt.last_added_date IS NULL OR rt.last_added_date < ?");
    $stmt->execute([$todayStr]);
    $repeats = $stmt->fetchAll();

    foreach ($repeats as $repeat) {
        $details = json_decode($repeat['repetition_details'], true);
        $baseDate = $repeat['last_added_date'] ?? $repeat['original_due_date'];
        $nextDate = getNextOccurrence($repeat['repetition'], $details, $baseDate);

        if ($nextDate && $nextDate <= $todayStr) {
            // Insert new task occurrence
            $insertStmt = $pdo->prepare("INSERT INTO tasks (title, description, due_date, priority, status, category_id, order_index) VALUES (?, ?, ?, ?, 'Pending', ?, (SELECT COALESCE(MAX(order_index), 0) + 1 FROM tasks))");
            $insertStmt->execute([
                $repeat['title'],
                $repeat['description'],
                $nextDate,
                $repeat['priority'],
                $repeat['category_id']
            ]);

            // Update last_added_date
            $updateStmt = $pdo->prepare("UPDATE repeated_tasks SET last_added_date = ? WHERE id = ?");
            $updateStmt->execute([$nextDate, $repeat['id']]);
        }
    }

    echo "Repeat tasks processed successfully.\n";
} catch (Exception $e) {
    echo "Error processing repeat tasks: " . $e->getMessage() . "\n";
}
?>
