<?php
include_once("utilities/Classes.php");
include_once("utilities/CarStorage.php");

header('Content-Type: application/json');


$postData = json_decode(file_get_contents('php://input'), true);



$seats = isset($postData['seats']) && ctype_digit($postData['seats']) ? (int) $postData['seats'] : null;
$gear = isset($postData['gearType']) && in_array($postData['gearType'], ['Automatic', 'Manual'], true) ? $postData['gearType'] : null;
$minPrice = isset($postData['minPrice']) && ctype_digit($postData['minPrice']) ? (int) $postData['minPrice'] : null;
$maxPrice = isset($postData['maxPrice']) && ctype_digit($postData['maxPrice']) ? (int) $postData['maxPrice'] : null;


$carStorage = new CarStorage();
$allCars = $carStorage->findAll();

$errors = [];

if ($seats !== null && $seats < 1) {
    $errors[] = "Seats must be at least 1.";
}

if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
    $errors[] = "Minimum price cannot be greater than maximum price.";
}

if (!empty($errors)) {
    usort($allCars, fn($a, $b) => (int)($a['daily_price_huf']) <=> (int)($b['daily_price_huf']));
    echo json_encode(['success' => false, 'cars' =>array_values($allCars) , 'message' => implode(", ", $errors)]);
    exit;
}



$filteredCars = array_filter($allCars, function ($car) use ($seats, $gear, $minPrice, $maxPrice) {
    $matches = true;

    if ($seats !== null) {
        $matches = $matches && $car['passengers'] === $seats;
    }

    if ($gear !== null) {
        $matches = $matches && $car['transmission'] === $gear;
    }

    if ($minPrice !== null) {
        $matches = $matches && $car['daily_price_huf'] >= $minPrice;
    }

    if ($maxPrice !== null) {
        $matches = $matches && $car['daily_price_huf'] <= $maxPrice;
    }

    return $matches;
});

if (!empty($filteredCars)) {


  usort($filteredCars, fn($a, $b) => (int)($a['daily_price_huf']) <=> (int)($b['daily_price_huf']));
    echo json_encode(['success' => true, 'cars' => array_values($filteredCars)]);
} else {

  usort($allCars, fn($a, $b) => (int)($a['daily_price_huf']) <=> (int)($b['daily_price_huf']));
    echo json_encode(['success' => false, 'cars' =>array_values($allCars)   , 'message' => 'No cars match your criteria.']);
}
