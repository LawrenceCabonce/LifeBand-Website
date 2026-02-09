<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['acc_email']);
    $password = $_POST['acc_password'];

    $sql = "SELECT * FROM tbl_accounts WHERE acc_email='$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['acc_password'])) {
            $_SESSION['acc_id'] = $user['acc_id'];
            $_SESSION['acc_name'] = $user['acc_name'];
            $_SESSION['acc_role'] = $user['acc_role'];
            $_SESSION['acc_email'] = $user['acc_email'];
            $_SESSION['acc_region'] = $user['acc_region'];
            $_SESSION['acc_birthdate'] = $user['acc_birthdate'];
            header('Location: Homepage.html?user=' . urlencode($user['acc_name']) . '&email=' . urlencode($user['acc_email']) . '&region=' . urlencode($user['acc_region']) . '&dob=' . urlencode($user['acc_birthdate']) . '&role=' . urlencode($user['acc_role']));
            exit;
        } else {
            header('Location: index.html?error=invalid');
            exit;
        }
    } else {
        header('Location: index.html?error=notfound');
        exit;
    }
}
?>