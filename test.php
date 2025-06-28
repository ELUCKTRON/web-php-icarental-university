<?php

include_once("utilities/Classes.php");
include_once("utilities/UsersStorage.php");

session_start();

$usersStorage = new UsersStorage();

print_r($usersStorage->findAll());

print_r("-------------------------------------------- </br>");

print_r($usersStorage->getContactsByEmail("saeedkhanloo@yahoo.com"))




?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>

</body>
</html>
