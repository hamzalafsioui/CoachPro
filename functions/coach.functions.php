<?php
require_once __DIR__ . '/../config/database.php';

function getCoachProfile($userId)
{
    global $conn;

    $sql = "SELECT cp.*, u.firstname, u.lastname, u.email, u.phone 
            FROM coach_profiles cp
            JOIN users u ON cp.user_id = u.id 
            WHERE u.id = ? AND u.role_id = (SELECT id FROM roles WHERE name = 'coach')";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}
