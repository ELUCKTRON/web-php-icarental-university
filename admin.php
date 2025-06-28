<?php
include_once("utilities/Classes.php");
include_once("utilities/BookingStorage.php");
include_once("utilities/CarStorage.php");
include_once("utilities/UsersStorage.php");

session_start();

// Mocked user session for demonstration

$adminSession =  $_SESSION['user']  ?? null;
$is_admin = filter_var($_SESSION['is_admin'] ?? false, FILTER_VALIDATE_BOOLEAN);

if (!$adminSession) {

    header("Location: index.php");
    exit;
}

if (!$is_admin) {

    header("Location: index.php");
    exit;
}

$bookingStorage = new BookingStorage();
$carStorage = new CarStorage();
$usersStorage = new UsersStorage();

$adminData = array_values($usersStorage->getContactsByEmail($adminSession))[0] ?? null;


// Fetch user bookings
$usersBookings = $bookingStorage->findAll();
$bookings = array_map(
    function ($booking) {
        return new Booking($booking["id"],$booking["start_date"], $booking["end_date"], $booking["user_email"], $booking["car_id"]);
    },
    $usersBookings
);

usort($bookings, fn($a, $b) => strtotime($a->start_date) <=> strtotime($b->start_date));

$cs = new CarStorage();
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
    $cs->findAll()
);
usort($listOfCars, fn($a, $b) => (int)($a->daily_price_huf) <=> (int)($b->daily_price_huf));



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page - iCarRental</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container header-container">
            <h1 class="logo">iCarRental</h1>
            <?php if ($adminSession) : ?>
            <a href="user.php" class="round-picture">
                <img src="pictures/users/<?=$adminData["image"]?>" alt="User Picture">
            </a>
            <nav class="nav-buttons">
                <a href="LogOut.php" class="button login">LogOut</a>
            </nav>
            <?php endif; ?>

        </div>
    </header>



    <!-- Main Content -->
    <main class="main">
        <!-- User Info Section -->



        <div class="AdminPageDetails">
        <button id="add-car-btn" class="buttons">Add New Car</button>
        <button id="mainPage" class="buttons" >Main-Page</button>

            <div id="add-car-form" class="filter-group" style="display: none;" >
                <h3>-</h3>

                <label for="brand">Brand</label>
                <input type="text" id="brand" placeholder="Car Brand" required>

                <label for="model">Model</label>
                <input type="text" id="model" placeholder="Car Model" required>

                <label for="year">Year</label>
                <input type="number" id="year" placeholder="Year" min="1886" required>

                <label for="transmission">Transmission</label>
                <select id="transmission" required>
                    <option value="">Select Transmission</option>
                    <option value="Automatic">Automatic</option>
                    <option value="Manual">Manual</option>
                </select>

                <label for="fuel_type">Fuel Type</label>
                <select id="fuel_type" required>
                    <option value="">Select Fuel Type</option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Electric">Electric</option>
                    <option value="Hybrid">Hybrid</option>
                </select>

                <label for="passengers">Passengers</label>
                <input type="number" id="passengers" placeholder="Number of Passengers" min="1" required>

                <label for="daily_price_huf">Daily Price (HUF)</label>
                <input type="number" id="daily_price_huf" placeholder="Daily Price in HUF" min="1" required>

                <label for="image">Image</label>
                <input type="file" id="image" accept="image/*" required>

                <button id="save-car-btn" class="buttons">Save Car</button>
                <button id="cancel-save-car-btn" class="buttons">Cancel</button>
            </div>
            <span id="form-result" style="color: red;"></span>

        </div>


        <section id="car-listings" class="car-listing container">

            <!-- Repeat this block for each car -->
            <?php foreach ($listOfCars as $car) : ?>
            <div class="car-card">
                <div class="car-image">
                <img src="<?=$car->image?>" class="car-image image" alt="Car Image">
                <p class="data"><?=$car->daily_price_huf?> Ft</p>
                </div>
                <div class="car-info">
                <div class="content">
                    <div>
                    <h2><?=$car->brand?> <?=$car->model?></h2>
                    <p><?=$car->passengers?> seats - <?=$car->transmission?></p>
                    </div>
                    <div class="cars-buttons">
                    <button data-id="<?=$car->id?>" data-car="<?=htmlspecialchars(json_encode($car), ENT_QUOTES, 'UTF-8')?>"
                      class="button Edit">edit</button>
                    <button data-id="<?=$car->id?>"  class="button Delete">delete</button>
                    </div>

                </div>
                </div>
            </div>
            <?php endforeach; ?>


            <!-- Repeat ends -->
            </section>


            <section class="filters container">

            <div class="filter-wrapper">
                <div class="filter-group">
                    <label for="seats">Seats</label>
                    <div class="seats">
                        <input type="number" id="seats" name="seats" min="1">
                    </div>
                </div>
                <div class="filter-group">
                    <label for="gear-type">Gear type</label>
                    <select id="gear-type" name="gear-type">
                        <option value="">Select</option>
                        <option value="automatic">Automatic</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="price-range">Price</label>
                    <div class="price-range">
                        <input type="number" id="price-min" name="price-min" placeholder="Min">
                        <span>-</span>
                        <input type="number" id="price-max" name="price-max" placeholder="Max">
                    </div>
                </div>


                <div class="filter-group">
                    <label for="from">From</label>
                    <input type="date" id="from" name="from">
                </div>
                <div class="filter-group">
                    <label for="until">Until</label>
                    <input type="date" id="until" name="until">
                </div>

                <div class="filter-group">
                    <label for="timeline">Timeline</label>
                    <div class="available">
                        <label>
                            <input type="radio" name="available" value="available">
                            <span>Available</span>
                        </label>
                        <label>
                            <input type="radio" name="available" value="passed">
                            <span>Passed</span>
                        </label>
                        <label>
                            <input type="radio" name="available" value="all">
                            <span>All</span>
                        </label>
                    </div>
            </div>
            <button id="filter-button" class="button filter">Filter</button>
            <button id="filter-reset" class="button filter">reset</button>
            </div>
            </section>

        <div class="AdminPageDetails">
            <h2 class="listing-title">All Car Bookings</h2>
        <span id="result"></span></div>
        <!-- Bookings Section -->





        <section id="car-bookings" class="booking-listing container">
             <span style="color: red;" id="error" ></span>
            <?php foreach ($bookings as $booking) :
                $carInfo = $carStorage->findById(($booking->car_id));
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

                $userData = array_values($usersStorage->getContactsByEmail($booking->user_email))[0] ?? null;
                $user = $userData ? new User(
                  $userData["full_name"],
                  $userData["email"],
                  $userData["password"],
                  $userData["is_admin"],
                  $userData["image"],
                  $userData["id"]
              )
              : null;




                ?>

              <div class="booking-data">

                    <div class="container user-info">
                        <div class="user-photo">
                            <img src="pictures/users/<?=$user->image?>" alt="User Photo" data-id="<?=$user->id?>" id="user-image" />
                            <input type="file" id="file-input" accept="image/*" style="display: none;" />
                        </div>
                        <div class="user-details">
                            <h2 id="user-name"><?= $user->full_name ?></h2>
                            <input type="text" id="text-input" style="display: none;" placeholder="Edit your name">
                            <p>Email: <?=$user->email ?></p>
                            <p><?= $user->is_admin ? 'Admin' : 'Regular User'; ?></p>
                        </div>
                    </div>

                  <div class="car-card">
                      <div class="car-image">
                          <img src="<?= $car ? $car->image : 'pictures/cars/placeholder.png' ?>" class="car-image image" alt="Car Image">
                          <p class="data"><?= "{$booking->start_date} - {$booking->end_date}" ?></p>
                      </div>
                      <div class="car-info">
                          <div class="content">
                              <div>
                                  <h2><?= $car ? "{$car->brand} {$car->model}" : 'Unknown Car' ?></h2>
                                  <p><?= $car ? "{$car->passengers} seats - {$car->transmission}" : 'Details not available' ?></p>
                              </div>
                              <?php if (strtotime($booking->end_date) > time()) : ?>
                              <button class="button Cancel" data-id="<?=$booking->id?>">Cancel</button>
                              <?php endif; ?>
                          </div>
                      </div>
                  </div>

                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <script src="scripts/admin.js"></script>
</body>
</html>
