<?php

include_once("utilities/Classes.php");
include_once("utilities/BookingStorage.php");
include_once("utilities/CarStorage.php");

session_start();

header('Content-Type: application/json');

$postData = json_decode(file_get_contents('php://input'), true);
$carId = isset($postData['car_id']) && (trim($postData['car_id'])) ? trim($postData['car_id']) : null;
$startDate = isset($postData['start_date']) && strtotime(trim($postData['start_date'])) ? trim($postData['start_date']) : null;
$endDate = isset($postData['end_date']) && strtotime(trim($postData['end_date'])) ? trim($postData['end_date']) : null;

$currentDate = strtotime(date('Y-m-d'));

if (!$startDate || !$endDate || strtotime($startDate) < $currentDate || strtotime($endDate) < strtotime($startDate)) {

    die(json_encode(['success' => false, 'message' => 'Invalid date range from' . "$startDate" . "until" . $endDate ]));
}

if (!$carId) {
    die(json_encode(['success' => false, 'message' => 'booking ID NOT SET']));
}

$userSession = $_SESSION['user'] ?? null;

if (!$userSession) {
    die(json_encode(['success' => false, 'message' => 'USER NOT SIGNED IN']));
}


$carStorage = new CarStorage();
$car = $carStorage->findById($carId);

if (!$car) {
    die(json_encode(['success' => false, 'message' => 'Invalid car ID']));
}


$bookingStorage = new BookingStorage();
$newBooking = new Booking(0, $startDate, $endDate, $userSession, $carId);
$bookingStorage->add($newBooking);

die(json_encode(['success' => true, 'car_id' => $carId]));
