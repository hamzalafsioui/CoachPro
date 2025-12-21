<?php
require_once __DIR__ . '/../config/database.php';

function getSportifStats(int $sportifId): array
{
    global $conn;

    $stats = [
        'workouts' => 0,
        'calories' => '4,250', // Hardcoded
        'active_minutes' => 340 // Hardcoded
    ];

    // Count workouts (completed or confirmed reservations)
    $sql = "SELECT COUNT(*) as total 
            FROM reservations r
            JOIN statuses s ON r.status_id = s.id
            WHERE r.sportif_id = ? AND s.name IN ('confirmed', 'completed')";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $sportifId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $stats['workouts'] = $row['total'];
        }
        $stmt->close();
    }

    return $stats;
}

function getSportifUpcomingSession(int $sportifId): ?array
{
    global $conn;

    $sql = "
        SELECT 
            r.id,
            u.firstname as coach_firstname,
            u.lastname as coach_lastname,
            a.date,
            a.start_time,
            a.end_time,
            s.name as status,
            GROUP_CONCAT(sp.name SEPARATOR ', ') as sports
        FROM reservations r
        JOIN availabilities a ON r.availability_id = a.id
        JOIN users u ON r.coach_id = u.id
        JOIN statuses s ON r.status_id = s.id
        LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
        LEFT JOIN sports sp ON sp.id = cs.sport_id
        WHERE r.sportif_id = ? 
          AND (a.date > CURDATE() OR (a.date = CURDATE() AND a.start_time > CURTIME()))
          AND s.name = 'confirmed'
        GROUP BY r.id
        ORDER BY a.date ASC, a.start_time ASC
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return null;

    $stmt->bind_param("i", $sportifId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $timestamp = strtotime($row['date']);
        $today = strtotime(date('Y-m-d'));
        $tomorrow = strtotime('+1 day', $today);

        if ($timestamp === $today) {
            $displayDate = 'Today';
        } elseif ($timestamp === $tomorrow) {
            $displayDate = 'Tomorrow';
        } else {
            $displayDate = date('M j', $timestamp);
        }

        return [
            'coach' => $row['coach_firstname'] . ' ' . $row['coach_lastname'],
            'type' => $row['sports'] ?: 'Personal Training',
            'date' => $displayDate,
            'time' => date('H:i', strtotime($row['start_time'])) . ' - ' . date('H:i', strtotime($row['end_time'])),
            'avatar' => strtoupper($row['coach_firstname'][0] . $row['coach_lastname'][0])
        ];
    }

    return null;
}

function getSportifRecentActivity(int $sportifId, int $limit = 3): array
{
    global $conn;

    $sql = "
        SELECT 
            r.id,
            u.firstname as coach_firstname,
            u.lastname as coach_lastname,
            a.date,
            s.name as status,
            GROUP_CONCAT(sp.name SEPARATOR ', ') as sports
        FROM reservations r
        JOIN availabilities a ON r.availability_id = a.id
        JOIN users u ON r.coach_id = u.id
        JOIN statuses s ON r.status_id = s.id
        LEFT JOIN coach_sports cs ON cs.coach_id = r.coach_id
        LEFT JOIN sports sp ON sp.id = cs.sport_id
        WHERE r.sportif_id = ?
        GROUP BY r.id
        ORDER BY a.date DESC, a.start_time DESC
        LIMIT ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) return [];

    $stmt->bind_param("ii", $sportifId, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $timestamp = strtotime($row['date']);
        $today = strtotime(date('Y-m-d'));
        $diff = floor(($today - $timestamp) / (60 * 60 * 24));

        if ($diff == 0) $displayDate = 'Today';
        elseif ($diff == 1) $displayDate = 'Yesterday';
        elseif ($diff < 7) $displayDate = $diff . ' days ago';
        else $displayDate = date('M j', $timestamp);

        $activities[] = [
            'title' => $row['sports'] ?: 'Workout',
            'date' => $displayDate,
            'coach' => $row['coach_firstname'] . ' ' . $row['coach_lastname']
        ];
    }

    return $activities;
}

function getSportifWeeklyActivity(): array
{
    // Hardcoded
    return [
        ['day' => 'M', 'height' => '40%'],
        ['day' => 'T', 'height' => '70%'],
        ['day' => 'W', 'height' => '30%'],
        ['day' => 'T', 'height' => '85%'],
        ['day' => 'F', 'height' => '60%'],
        ['day' => 'S', 'height' => '90%'],
        ['day' => 'S', 'height' => '20%'],
    ];
}

function updateSportifProfile(int $userId, string $firstname, string $lastname, ?string $phone): bool
{
    global $conn;

    $sql = "UPDATE users SET firstname = ?, lastname = ?, phone = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $firstname, $lastname, $phone, $userId);

    return $stmt->execute();
}



