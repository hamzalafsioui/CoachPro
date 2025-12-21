<?php

require_once __DIR__ . '/../config/database.php';

function getCoachStats($coachId)
{
    global $conn;

    $stats = [
        'total_sessions' => 0,
        'total_clients' => 0,
        'rating' => 0.0
    ];

    // Total Sessions (Completed or Confirmed)
    $sql_sessions = "SELECT COUNT(*) as total 
                     FROM reservations r
                     JOIN statuses s ON r.status_id = s.id
                     WHERE r.coach_id = ? AND s.name IN ('confirmed', 'completed')";
    $stmt = $conn->prepare($sql_sessions);
    if ($stmt) {
        $stmt->bind_param("i", $coachId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $stats['total_sessions'] = $row['total'];
        }
        $stmt->close();
    }

    // Total Unique Clients
    $sql_clients = "SELECT COUNT(DISTINCT sportif_id) as total 
                    FROM reservations 
                    WHERE coach_id = ?";
    $stmt = $conn->prepare($sql_clients);
    if ($stmt) {
        $stmt->bind_param("i", $coachId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $stats['total_clients'] = $row['total'];
        }
        $stmt->close();
    }

    // Average Rating 
    $sql_rating = "SELECT rating_avg FROM coach_profiles WHERE id = ?";
    $stmt = $conn->prepare($sql_rating);
    if ($stmt) {
        $stmt->bind_param("i", $coachId);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $stats['rating'] = number_format((float)$row['rating_avg'], 1);
        }
        $stmt->close();
    }

    return $stats;
}
