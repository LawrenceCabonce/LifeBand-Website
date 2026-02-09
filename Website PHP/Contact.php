<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['cm_name']);
    $email = mysqli_real_escape_string($conn, $_POST['cm_email']);
    $phone = mysqli_real_escape_string($conn, $_POST['cm_phone']);
    $subject = mysqli_real_escape_string($conn, $_POST['cm_subject']);
    $message = mysqli_real_escape_string($conn, $_POST['cm_message']);
    $sql = "INSERT INTO tbl_contact_messages (cm_name, cm_email, cm_phone, cm_subject, cm_message) VALUES ('$name', '$email', '$phone', '$subject', '$message')";
    if (mysqli_query($conn, $sql)) {
        header('Location: Contact.html?success=1');
        exit;
    } else {
        header('Location: Contact.html?error=1');
        exit;
    }
}
?>