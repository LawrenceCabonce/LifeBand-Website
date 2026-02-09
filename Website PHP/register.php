<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['acc_name']);
    $email = mysqli_real_escape_string($conn, $_POST['acc_email']);
    $password = password_hash($_POST['acc_password'], PASSWORD_DEFAULT);
    $region = mysqli_real_escape_string($conn, $_POST['acc_region']);
    $dob = mysqli_real_escape_string($conn, $_POST['acc_birthdate']);
    $role = mysqli_real_escape_string($conn, $_POST['acc_role']);

    $sql = "INSERT INTO tbl_accounts (acc_name, acc_email, acc_password, acc_region, acc_birthdate, acc_role) VALUES ('$name', '$email', '$password', '$region', '$dob', '$role')";
    if (mysqli_query($conn, $sql)) {
        header('Location: index.html');
        exit;
    } else {
        header('Location: Account_Registration.html?error=1');
        exit;
    }
}
?>