<?php

include_once("utilities/Classes.php");
include_once("utilities/UsersStorage.php");

session_start();

$usersStorage = new UsersStorage();

$allusers = array_map(function($user) { $user["email"]; }, $usersStorage->findAll());

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim(filter_input(INPUT_POST, 'name', FILTER_DEFAULT));
    if ($firstName) {
        $firstName = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
    }
    if (empty($firstName)) {
        $errors[] = "First Name is required.";
    } elseif (strlen($firstName) < 2) {
        $errors[] = "First Name must be at least 2 characters.";
    }

    $lastName = trim(filter_input(INPUT_POST, 'lastName', FILTER_DEFAULT));
    if ($lastName) {
        $lastName = htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8');
    }
    if (empty($lastName)) {
        $errors[] = "Last Name is required.";
    } elseif (strlen($lastName) < 2) {
        $errors[] = "Last Name must be at least 2 characters.";
    }

    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    $password = trim($_POST['password']);
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        if (in_array($email, $allusers)) {
            $errors[] = "This email is already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $newUser = new User("{$firstName} {$lastName}", $email, $hashedPassword, false, "default.PNG",0);

            $usersStorage->add($newUser);

                $_SESSION['user'] = $newUser->email;
                $_SESSION['is_admin'] = $newUser->is_admin;

            header("Location: user.php");
            exit;
        }
    }

    $errorString = urlencode(implode(", ", $errors));
    header("Location: loginAndSignUp.php?errors=$errorString");
    exit;
} else {
    header("Location: loginAndSignUp.php");
    exit;
}
