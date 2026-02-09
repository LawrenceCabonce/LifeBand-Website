<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $feedback = mysqli_real_escape_string($conn, $_POST['fback_content']);
    $name = mysqli_real_escape_string($conn, $_POST['fback_name']);
    $rating = isset($_POST['fback_rating']) ? (int)$_POST['fback_rating'] : 0;
    
    if ($rating < 1 || $rating > 5) {
        header('Location: Feedback_Evaluation.html?error=norating');
        exit;
    }
    
    $sql = "INSERT INTO tbl_feedback (fback_name, fback_content, fback_rating) VALUES ('$name', '$feedback', $rating)";
    
    if (mysqli_query($conn, $sql)) {
        header('Location: Feedback_Evaluation.html?success=1');
        exit;
    } else {
        header('Location: Feedback_Evaluation.html?error=db&msg=' . urlencode(mysqli_error($conn)));
        exit;
    }
}
?>