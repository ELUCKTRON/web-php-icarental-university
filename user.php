<?php
include_once("utilities/Classes.php");
include_once("utilities/BookingStorage.php");
include_once("utilities/CarStorage.php");
include_once("utilities/UsersStorage.php");

session_start();

// Mocked user session for demonstration

$userSession =  $_SESSION['user']  ?? null;

if (!$userSession) {

    header("Location: index.php");
    exit;
}



$bookingStorage = new BookingStorage();
$carStorage = new CarStorage();
$usersStorage = new UsersStorage();

$userData = array_values($usersStorage->getContactsByEmail($userSession))[0] ?? null;


$user = $userData ? new User(
        $userData["full_name"],
        $userData["email"],
        $userData["password"],
        $userData["is_admin"],
        $userData["image"],
        $userData["id"]
    )
    : null;

// Fetch user bookings
$userBookings = $bookingStorage->getContactsByEmail($user->email);
$bookings = array_map(
    function ($booking) {
        return new Booking($booking["id"],$booking["start_date"], $booking["end_date"], $booking["user_email"], $booking["car_id"]);
    },
    $userBookings
);

usort($bookings, fn($a, $b) => strtotime($a->start_date) <=> strtotime($b->start_date));


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page - iCarRental</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/user.css">
</head>
<body>
    <!-- Header Section -->
    <header class="header">
        <div class="container header-container">
            <h1 class="logo">iCarRental</h1>
            <?php if ($userSession) : ?>
            <a href="user.php" class="round-picture">
                <img src="pictures/users/<?=$user->image?>" alt="User Picture">
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
        <section class="hero">
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
                <button id="edit" class="buttons">Edit</button>
                <button id="save" class="buttons" style="display: none;">Save</button>
                <span id="error" style="color: red;"></span>
            </div>
        </section>




        <h2 class="listing-title">Your Car Bookings</h2>
        <button id="book" class="buttons" >Book more</button>
        <!-- Bookings Section -->
        <section class="car-listing container">
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
                ?>
            <div class="car-card">
                <div class="car-image">
                    <img src="<?= $car ? $car->image : 'pictures/cars/placeholder.png' ?>" alt="Car Image">
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
            <?php endforeach; ?>
        </section>
    </main>


    <script src="scripts/user.js"></script>
</body>
</html>
