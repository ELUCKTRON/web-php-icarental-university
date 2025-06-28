<?php
include_once("utilities/Classes.php");
include_once("utilities/CarStorage.php");

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $user = $_SESSION['user'] ?? null;
        $is_admin = filter_var($_SESSION['is_admin'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (!$user) {
            die(json_encode(['success' => false, 'message' => 'User not signed in']));
        }

        if (!$is_admin) {
            die(json_encode(['success' => false, 'message' => 'User is not ADMIN']));
        }

    $carStorage = new CarStorage();

    // Validate inputs
    $carId = isset($_POST['carId']) ? $_POST['carId'] : null;
    $brand = isset($_POST['brand']) && is_string($_POST['brand']) && strlen($_POST['brand']) > 0 ? htmlspecialchars($_POST['brand'], ENT_QUOTES, 'UTF-8') : null;
    $model = isset($_POST['model']) && is_string($_POST['model']) && strlen($_POST['model']) > 0 ? htmlspecialchars($_POST['model'], ENT_QUOTES, 'UTF-8') : null;
    $year = isset($_POST['year']) && ctype_digit($_POST['year']) && (int)$_POST['year'] >= 1900 ? (int)$_POST['year'] : null;
    $transmission = isset($_POST['transmission']) && in_array($_POST['transmission'], ['Automatic', 'Manual'], true) ? $_POST['transmission'] : null;
    $fuel_type = isset($_POST['fuel_type']) && in_array($_POST['fuel_type'], ['Petrol', 'Diesel', 'Electric', 'Hybrid'], true) ? $_POST['fuel_type'] : null;
    $passengers = isset($_POST['passengers']) && ctype_digit($_POST['passengers']) && (int)$_POST['passengers'] > 0 ? (int)$_POST['passengers'] : null;
    $daily_price_huf = isset($_POST['daily_price_huf']) && ctype_digit($_POST['daily_price_huf']) && (int)$_POST['daily_price_huf'] > 0 ? (int)$_POST['daily_price_huf'] : null;
    $image = $_FILES['image'] ?? null;

    // Validate required fields
    if (!$brand || !$model || !$year || !$transmission || !$fuel_type || !$passengers || !$daily_price_huf) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing fields.']);
        exit;
    }

    // Validate file upload
    if ($image) {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

        if (!in_array($imageExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG and PNG images are allowed.']);
            exit;
        }

        if ($image['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size exceeds the maximum limit of 2MB.']);
            exit;
        }
    }

    // Check for valid car ID if editing
    $allcars = $carStorage->findAll();
    $carIds = array_map(fn($car) => $car['id'], $allcars);

    if ($carId !== null && !in_array($carId, $carIds)) {
        echo json_encode(['success' => false, 'message' => " Invalid car ID $carId " ]);
        exit;
    }



    $carImage = null;

    if($image){
          // Validate the uploaded image extension
      $allowedExtensions = ['jpg', 'jpeg', 'png'];
      $imageExtension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));

      if (!in_array($imageExtension, $allowedExtensions)) {
          echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG and PNG images are allowed.']);
          exit;
      }

      // Save the uploaded image
      $uploadDir = 'pictures/cars/';
      $uploadFile = $uploadDir . $brand . "-" . $model . "." . $imageExtension;

      // Move the uploaded file to the desired directory
      if (!move_uploaded_file($image['tmp_name'], $uploadFile)) {
          echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
          exit;
      }
      $carImage = $uploadFile;
    }else {
        $carImage = 'pictures/cars/default.png  ';
    }

    $foundedCar = ($carId !== null)? $carStorage->findById($carId) : null;
    if($foundedCar){
        $foundedCarImage = ($image)? $carImage : $foundedCar["image"];
    }

    // Save car details
    $carData = [
        'brand' => $brand,
        'model' => $model,
        'year' => (int)$year,
        'transmission' => $transmission,
        'fuel_type' => $fuel_type,
        'passengers' => (int)$passengers,
        'daily_price_huf' => (int)$daily_price_huf,
        'image' =>($carId === null)? $carImage : $foundedCarImage ,
        'id' =>($carId === null)? 0 : $foundedCar["id"]
    ];

    // $newcar = new Car(0,$brand,$model,(int)$year,$transmission,$fuel_type,(int)$passengers,(int)$daily_price_huf,$uploadFile);

    if($carId === null){
        $carStorage->add($carData);
    }else{
        $carStorage->update($carId,$carData);
    }


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

    echo json_encode(['success' => true, "allcars" => $listOfCars, 'message' => 'Car successfully added or edited']);
    exit;
}
