<?php
session_start();
include 'config.php';

if (!isset($_SESSION['acc_role']) || $_SESSION['acc_role'] != 'Admin') {
    header('Location: index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_alert'])) {
        $title = mysqli_real_escape_string($conn, $_POST['alerts_title']);
        $message = mysqli_real_escape_string($conn, $_POST['alerts_detail']);
        $severity = mysqli_real_escape_string($conn, $_POST['alerts_severity']);
        $sql = "INSERT INTO tbl_system_alerts (alerts_title, alerts_detail, alerts_severity) VALUES ('$title', '$message', '$severity')";
        mysqli_query($conn, $sql);
        header('Location: Admin_Control_Panel.php?success=alert_added');
        exit;

    } elseif (isset($_POST['delete_alert'])) {
        $id = (int)$_POST['alerts_id'];
        $sql = "DELETE FROM tbl_system_alerts WHERE alerts_id=$id";
        mysqli_query($conn, $sql);
        header('Location: Admin_Control_Panel.php?success=alert_deleted');
        exit;

    } elseif (isset($_POST['add_status'])) {
        $title = mysqli_real_escape_string($conn, $_POST['system_title']);
        $description = mysqli_real_escape_string($conn, $_POST['system_description']);
        $status = mysqli_real_escape_string($conn, $_POST['system_status']);
        
        // Insert into tbl_system_status with current date
        $sql = "INSERT INTO tbl_system_status (system_title, system_description, system_status, status_date) VALUES ('$title', '$description', '$status', NOW())";
        if (mysqli_query($conn, $sql)) {
            header('Location: Admin_Control_Panel.php?success=status_added');
        } else {
            header('Location: Admin_Control_Panel.php?error=status_failed');
        }
        exit;
    }
}
?>