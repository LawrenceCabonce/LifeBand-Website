<?php
session_start();
include 'config.php';

if (!isset($_SESSION['acc_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $acc_id = $_SESSION['acc_id'];
    $name = mysqli_real_escape_string($conn, $_POST['acc_name']);
    $email = mysqli_real_escape_string($conn, $_POST['acc_email']);
    $region = mysqli_real_escape_string($conn, $_POST['acc_region']);
    $dob = mysqli_real_escape_string($conn, $_POST['acc_birthdate']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['acc_password'] ?? '';

    $verify_sql = "SELECT acc_password FROM tbl_accounts WHERE acc_id=$acc_id";
    $verify_result = mysqli_query($conn, $verify_sql);
    $user = mysqli_fetch_assoc($verify_result);

    if (!password_verify($current_password, $user['acc_password'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }

    if (!empty($new_password)) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE tbl_accounts SET acc_name='$name', acc_email='$email', acc_region='$region', acc_birthdate='$dob', acc_password='$password_hash' WHERE acc_id=$acc_id";
    } else {
        $sql = "UPDATE tbl_accounts SET acc_name='$name', acc_email='$email', acc_region='$region', acc_birthdate='$dob' WHERE acc_id=$acc_id";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['acc_name'] = $name;
        $_SESSION['acc_email'] = $email;
        $_SESSION['acc_region'] = $region;
        $_SESSION['acc_birthdate'] = $dob;

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Account updated successfully']);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $acc_id = $_SESSION['acc_id'];
    $sql = "SELECT acc_id, acc_name, acc_email, acc_region, acc_birthdate FROM tbl_accounts WHERE acc_id=$acc_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'acc_id' => $user['acc_id'],
            'acc_name' => $user['acc_name'],
            'acc_email' => $user['acc_email'],
            'acc_region' => $user['acc_region'],
            'acc_birthdate' => $user['acc_birthdate']
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }
    exit;
}
?>
