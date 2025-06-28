<?php
include_once("utilities/Classes.php");
include_once("utilities/BookingStorage.php");
include_once("utilities/CarStorage.php");
include_once("utilities/UsersStorage.php");

header('Content-Type: application/json');
session_start();

$postData = json_decode(file_get_contents('php://input'), true);

// Initialize storages
$bookingStorage = new BookingStorage();
$carStorage = new CarStorage();
$userStorage = new UsersStorage();

// Fetch bookings and construct $allResult
$allBookings = $bookingStorage->findAll();
$bookings = array_map(
    function ($booking) {
        return new Booking($booking["id"], $booking["start_date"], $booking["end_date"], $booking["user_email"], $booking["car_id"]);
    },
    $allBookings
);

usort($bookings, fn($a, $b) => strtotime($a->start_date) <=> strtotime($b->start_date));

$allResult = [];
foreach ($bookings as $booking) {
    $carInfo = $carStorage->findById((int)($booking->car_id));
    $car = $carInfo ? new Car(
        $carInfo["id"],
        $carInfo["brand"],
        $carInfo["model"],
        $carInfo["year"],
        $carInfo["transmission"],
        $carInfo["fuel_type"],
        $carInfo["passengers"],
        $carInfo["daily_price_huf"],
        $carInfo["image"]
    ) : null;

    $userData = array_values($userStorage->getContactsByEmail($booking->user_email))[0] ?? null;
    $user = $userData ? new User(
        $userData["full_name"],
        $userData["email"],
        $userData["password"],
        $userData["is_admin"],
        $userData["image"],
        $userData["id"]
    ) : null;

    $allResult[] = ["car" => $car, "user" => $user, "booking" => $booking];
}

// Extract and validate inputs
$seats = isset($postData['seats']) && ctype_digit($postData['seats']) ? (int)$postData['seats'] : null;
$gear = isset($postData['gearType']) && in_array($postData['gearType'], ['Automatic', 'Manual'], true) ? $postData['gearType'] : null;
$minPrice = isset($postData['minPrice']) && ctype_digit($postData['minPrice']) ? (int)$postData['minPrice'] : null;
$maxPrice = isset($postData['maxPrice']) && ctype_digit($postData['maxPrice']) ? (int)$postData['maxPrice'] : null;
$fromDate = isset($postData['from']) && strtotime($postData['from']) ? $postData['from'] : null;
$untilDate = isset($postData['until']) && strtotime($postData['until']) ? $postData['until'] : null;
$timeline = isset($postData['available']) && in_array($postData['available'], ['available', 'passed', 'all'], true) ? $postData['available'] : 'all';

$errors = [];

// Validate inputs
if ($seats !== null && $seats < 1) {
    $errors[] = "Seats must be at least 1.";
}

if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
    $errors[] = "Minimum price cannot be greater than maximum price.";
}

if ($fromDate && $untilDate && strtotime($fromDate) > strtotime($untilDate)) {
    $errors[] = "Invalid date range.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'result' => $allResult, 'message' => implode(", ", $errors)]);
    exit;
}

// Filter logic
$filteredResult = array_filter($allResult, function ($data) use ($seats, $gear, $minPrice, $maxPrice, $fromDate, $untilDate, $timeline) {
    $car = $data["car"];
    $booking = $data["booking"];

    if (!$car || !$booking) return false;

    $matches = true;

    if ($seats !== null) {
        $matches = $matches && $car->passengers === $seats;
    }

    if ($gear !== null) {
        $matches = $matches && $car->transmission === $gear;
    }

    if ($minPrice !== null) {
        $matches = $matches && $car->daily_price_huf >= $minPrice;
    }

    if ($maxPrice !== null) {
        $matches = $matches && $car->daily_price_huf <= $maxPrice;
    }

    if ($fromDate !== null) {
        $matches = $matches && strtotime($booking->start_date) >= strtotime($fromDate);
    }

    if ($untilDate !== null) {
        $matches = $matches && strtotime($booking->end_date) <= strtotime($untilDate);
    }

    $currentDate = strtotime(date('Y-m-d'));
    if ($timeline === 'available') {
        $matches = $matches && strtotime($booking->end_date) >= $currentDate;
    } elseif ($timeline === 'passed') {
        $matches = $matches && strtotime($booking->end_date) < $currentDate;
    }

    return $matches;
});

// Send results
if (!empty($filteredResult)) {
    echo json_encode(['success' => true, 'result' => array_values($filteredResult)]);
    exit;
} else {
    echo json_encode(['success' => false, 'result' => $allResult, 'message' => 'No bookings match your criteria.']);
    exit;
}
