<?php

include_once("utilities/Classes.php");
include_once("utilities/UsersStorage.php");

session_start();

$usersStorage = new UsersStorage();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']);

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {

        $user = array_values($usersStorage->getContactsByEmail($email))[0];

      //  $user = reset($usersStorage->getContactsByEmail($email));

        if ($user) {


            if (password_verify($password, $user['password'])) {
              $_SESSION['user'] = $user['email'];
              $_SESSION['is_admin'] = $user["is_admin"];
                header("Location: user.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }
    }

    $errorString = urlencode(implode(", ", $errors));
    header("Location: loginAndSignUp.php?errors=$errorString");
    exit;
} else {
    header("Location: loginAndSignUp.php");
    exit;
}
