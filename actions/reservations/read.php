<?php
session_start();
require_once '../../functions/reservation.functions.php';
require_once '../../functions/coach.functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}


$coachId = getCoachIdByUserId($_SESSION['user_id']);


if (!$coachId) {
    echo json_encode(['success' => false, 'message' => 'Coach profile not found']);
    exit;
}

$reservations = getCoachReservations($coachId);

echo json_encode(['success' => true, 'data' => $reservations]);
