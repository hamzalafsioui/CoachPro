<?php
session_start();
require_once __DIR__ . '/../../functions/auth.functions.php';
require_once __DIR__ . '/../../functions/sportif.functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user']['id'];

if (deleteSportifAccount($userId)) {
    // Clear session
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Account deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete account']);
}
