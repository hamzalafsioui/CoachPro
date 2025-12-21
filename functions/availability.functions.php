<?php
require_once __DIR__ . '/../config/database.php';


function getCoachAvailabilities($coachId)
{
    global $conn;

    $sql = "
        SELECT *
        FROM availabilities
        WHERE coach_id = ?
        ORDER BY date ASC, start_time ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $coachId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


function getAvailabilityById($availabilityId)
{
    global $conn;

    $sql = "SELECT * FROM availabilities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $availabilityId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}


function createAvailability($coachId, $date, $startTime, $endTime)
{
    global $conn;

    $sql = "
        INSERT INTO availabilities (coach_id, date, start_time, end_time, is_available)
        VALUES (?, ?, ?, ?, 1)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $coachId, $date, $startTime, $endTime);

    return $stmt->execute();
}



function updateAvailability($availabilityId, $date, $startTime, $endTime, $isAvailable)
{
    global $conn;

    $sql = "
        UPDATE availabilities
        SET date = ?, start_time = ?, end_time = ?, is_available = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $date, $startTime, $endTime, $isAvailable, $availabilityId);

    return $stmt->execute();
}



function deleteAvailability($availabilityId)
{
    global $conn;

    $sql = "DELETE FROM availabilities WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $availabilityId);

    return $stmt->execute();
}
