<?php

include_once("utilities/Classes.php");
include_once("utilities/CarStorage.php");
include_once("utilities/UsersStorage.php");

session_start();

$userSession =  $_SESSION['user']  ?? null;

$usersStorage = new UsersStorage();

$userData = array_values($usersStorage->getContactsByEmail($userSession))[0] ?? null;

// Get the car ID from the query parameter
$carId = $_GET['carid'] ?? null;

$car = null;

$errors = [];
if (!$carId) {
    $errors[]=("Car ID not provided.");
}

$carStorage = new CarStorage();

if(empty($errors)){
  $carData = $carStorage->findById($carId);

  if (!$carData) {
    $errors[]=("Car not found.");
  }

  // Create a Car object from the data
  $car = new Car(
      $carData["id"],
      $carData["brand"],
      $carData["model"],
      $carData["year"],
      $carData["transmission"],
      $carData["fuel_type"],
      $carData["passengers"],
      $carData["daily_price_huf"],
      $carData["image"]
  );

}




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page - iCarRental</title>

    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/carDetails.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container header-container">
            <h1 class="logo">iCarRental</h1>
            <?php if ($userSession) : ?>
            <a href="user.php" class="round-picture">
                <img src="pictures/users/<?=$userData["image"]?>" alt="User Picture">
            </a>
            <nav class="nav-buttons">
                <a href="LogOut.php" class="button login">LogOut</a>
            </nav>
            <?php endif; ?>

        </div>
    </header>



    <!-- Main Content -->
    <main class="main">


        <section class="car-listing container">

        <?php if($car) : ?>
          <div class="car-details">
            <div class="detail-page-image">
            <img src="<?= $car->image ?>" alt="Car Image">
            </div>
            <div class="detail-page-informations">
            <h2><?= $car->brand ?> <?= $car->model ?> (<?= $car->year ?>)</h2>
            <p>Transmission: <?= $car->transmission ?></p>
            <p>Fuel Type: <?= $car->fuel_type ?></p>
            <p>Seats: <?= $car->passengers ?></p>
            <p>Price per day: <?= $car->daily_price_huf ?> Ft</p>
            <div class="detail-page-buttons">
              <form action="index.php">
              <button class="redirect_button" >Go back To Book</button>
              </form>
            </div>
            </div>


        </div>
        <?php else :?>
        <h1><?=implode(", ",$errors)?></h1>
        <?php endif?>
        </section>

    </main>

</body>
</html>
