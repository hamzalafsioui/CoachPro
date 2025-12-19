<?php
session_start();
require_once '../../config/database.php';
require_once '../../functions/coach.functions.php';
require_once '../../functions/user.functions.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $experience = (int)($_POST['experience'] ?? 0);

    $nameParts = explode(' ', $name, 2);
    $firstname = $nameParts[0];
    $lastname = $nameParts[1] ?? '';

    
    $result = updateCoachProfile($userId, $firstname, $lastname, $email, $phone, $bio, $experience);

    if ($result) {
       
        header("Location: ../../pages/coach/profile.php?status=success");
    } else {
        
        header("Location: ../../pages/coach/profile.php?status=error");
    }
    exit();
} else {
    header("Location: ../../index.php");
    exit();
}
