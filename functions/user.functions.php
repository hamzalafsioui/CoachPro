<?php
require_once __DIR__ . '/../config/database.php';


function getUserById($userId)
{
    global $conn;

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}


function getCoachProfileByUserId($userId)
{
    global $conn;

    $sql = "
        SELECT cp.*, u.firstname, u.lastname, u.email, u.phone
        FROM coach_profiles cp
        JOIN users u ON cp.user_id = u.id
        WHERE u.id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc();
}




function getUserReservations($userId)
{
    global $conn;

    $sql = "
        SELECT r.id, r.price, r.created_at, s.name AS status
        FROM reservations r
        JOIN statuses s ON r.status_id = s.id
        WHERE r.sportif_id = ?
        ORDER BY r.created_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


function createUser($firstname, $lastname, $email, $password, $roleId)
{
    global $conn;

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "
        INSERT INTO users (firstname, lastname, email, password, role_id)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashedPassword, $roleId);

    return $stmt->execute();
}

function verifyUserPassword($userId, $password)
{
    global $conn;

    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        return password_verify($password, $row['password']);
    }

    return false;
}

function updateUserPassword($userId, $newPassword)
{
    global $conn;

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $hashedPassword, $userId);

    return $stmt->execute();
}
