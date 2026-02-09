<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['acc_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$acc_id = $_SESSION['acc_id'];

$sql = "SELECT acc_id, acc_name, acc_email, acc_region, acc_birthdate FROM tbl_accounts WHERE acc_id=$acc_id";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'acc_id' => $user['acc_id'],
        'acc_name' => $user['acc_name'],
        'acc_email' => $user['acc_email'],
        'acc_region' => $user['acc_region'],
        'acc_birthdate' => $user['acc_birthdate']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not found']);
}

mysqli_close($conn);
?>
