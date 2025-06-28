<?php
include_once("utilities/UsersStorage.php");

header('Content-Type: application/json');

session_start();
$response = ['success' => false];

$userSession = $_SESSION['user'] ?? null;
if (!$userSession) {
    $response['message'] = 'Unauthorized action. No active session.';
    echo json_encode($response);
    exit;
}

$usersStorage = new UsersStorage();
$userData = array_values($usersStorage->getContactsByEmail($userSession))[0] ?? null;

if (!$userData || !isset($userData["id"])) {
    $response['message'] = 'User not found or invalid data.';
    echo json_encode($response);
    exit;
}
$userId = $_POST['user_id'] ?? null;
if ($userData["id"] !== $userId) {
     $response['message'] = 'Unauthorized action. userData["id"]: ' . $userData["id"] . ', userId: ' . $userId;
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    if (!$newName) {
        $response['message'] = 'Invalid input.';
        echo json_encode($response);
        exit;
    }

    $newImageName = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/pictures/users/';
        $newImageName = $userId . ".PNG";
        $uploadFile = $uploadDir . $newImageName;

        if ($_FILES['image']['size'] > 1048576) {
            $response['message'] = 'The uploaded file exceeds the size limit of 1 MB.';
            echo json_encode($response);
            exit;
        }

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $response['message'] = 'File move failed. Check directory permissions or available disk space.';
            echo json_encode($response);
            exit;
        }
    }

    $user = $usersStorage->findById($userId);
    if ($user) {
        $user['full_name'] = $newName;
        if ($newImageName) {
            $user['image'] = $newImageName;
        }

        $usersStorage->update($userId, $user);

        $response['success'] = true;
        $response['message'] = 'User updated successfully.';
        $response['newImage'] = $newImageName ?? null;
    } else {
        $response['message'] = 'User not found.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
