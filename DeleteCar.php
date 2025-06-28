<?php

include_once("utilities/Classes.php");
include_once("utilities/CarStorage.php");

session_start();

header('Content-Type: application/json');

$postData = json_decode(file_get_contents('php://input'), true);
$carId = isset($postData['car_id']) && !empty(trim($postData['car_id'])) ? trim($postData['car_id']) : null;

if (!$carId) {
    die(json_encode(['success' => false, 'message' => 'Car ID not set']));
}

$user = $_SESSION['user'] ?? null;
$is_admin = filter_var($_SESSION['is_admin'] ?? false, FILTER_VALIDATE_BOOLEAN);

if (!$user) {
    die(json_encode(['success' => false, 'message' => 'User not signed in']));
}

if (!$is_admin) {
    die(json_encode(['success' => false, 'message' => 'User is not ADMIN']));
}

$carStorage = new CarStorage();
$allcars = $carStorage->findAll();

$carIDS = array_map(fn($car) => $car['id'], $allcars);

if (!in_array($carId, $carIDS)) {
    die(json_encode(['success' => false, 'message' => 'Invalid car ID']));
}

$carStorage->delete($carId);

$listOfCars = array_map(
    function ($car) {
        return new Car(
            $car["id"],
            $car["brand"],
            $car["model"],
            $car["year"],
            $car["transmission"],
            $car["fuel_type"],
            $car["passengers"],
            $car["daily_price_huf"],
            $car["image"]
        );
    },
    $carStorage->findAll()
);

usort($listOfCars, fn($a, $b) => (int)($a->daily_price_huf) <=> (int)($b->daily_price_huf));

echo json_encode(['success' => true, "allcars" => $listOfCars, 'message' => 'Car successfully deleted']);
