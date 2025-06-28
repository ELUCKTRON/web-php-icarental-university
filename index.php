<?php

include_once("utilities/Classes.php");
include_once("utilities/CarStorage.php");
include_once("utilities/UsersStorage.php");

session_start();

$userSession =  $_SESSION['user']  ?? null;

$usersStorage = new UsersStorage();

$userData = array_values($usersStorage->getContactsByEmail($userSession))[0] ?? null;


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
    <title>iCarRental</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
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
            <?php else : ?>
            <nav class="nav-buttons">
                <a href="loginAndSignUp.php" class="button login">Login/Registration</a>
            </nav>
            <?php endif ?>
        </div>
    </header>
    <main class="main">
        <section class="hero">
            <div class="container">
                <h1>Rent cars easily!</h1>
            </div>
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
                            <option value="Automatic">Automatic</option>
                            <option value="Manual">Manual</option>
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
                    <button id="filter-button" class="button filter">Filter</button>
                    <button id="filter-reset" class="button filter">reset</button>

                </div>

        </section>

        <section class="date-selection">
                 <div class="filter-group">
                        <label for="from">From</label>
                        <input type="date" id="from" name="from">
                    </div>
                    <div class="filter-group">
                        <label for="until">Until</label>
                        <input type="date" id="until" name="until">
                    </div>
                    <span id="result"></span>
                    <div >
                    <button id="mybookings" class="redirect_button">My Bookings</button>
                    <?php if( $userSession && $userData["is_admin"]) :?>
                        <button id="adminPage" class="redirect_button">Admin Page</button>
                    <?php endif?>
                    </div>

        </section>

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
                    <button data-id="<?=$car->id?>" data-name="<?=$car->brand?> <?=$car->model?>" class="button book">Book</button>
                </div>
                </div>
            </div>
            <?php endforeach; ?>


            <!-- Repeat ends -->
        </section>
    </main>

    <script src="scripts/index.js"></script>
</body>
</html>
