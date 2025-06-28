<?php

include_once("utilities/Classes.php");
include_once("utilities/BookingStorage.php");

session_start();

header('Content-Type: application/json');

$postData = json_decode(file_get_contents('php://input'), true);
$bookingId = isset($postData['booking_id']) && !empty(trim($postData['booking_id'])) ? trim($postData['booking_id']) : null;

if (!$bookingId) {
    die(json_encode(['success' => false, 'message' => 'Booking ID not set']));
}

$user = $_SESSION['user'] ?? null;
$is_admin = filter_var($_SESSION['is_admin'] ?? false, FILTER_VALIDATE_BOOLEAN);

if (!$user) {
    die(json_encode(['success' => false, 'message' => 'User not signed in']));
}

$bookingStorage = new BookingStorage();

if ($is_admin) {
    $userBookings = $bookingStorage->findAll();
} else {
    $userBookings = $bookingStorage->getContactsByEmail($user);
}

$bookingIDS = array_map(
    fn($booking) => $booking['id'],
    $userBookings
);

if (!in_array($bookingId, $bookingIDS)) {
    die(json_encode(['success' => false, 'message' => 'Invalid booking ID']));
}

$deleted = $bookingStorage->delete($bookingId);


    echo json_encode(['success' => true, 'message' => 'Booking successfully deleted']);
