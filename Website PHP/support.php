<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['acc_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$acc_id = $_SESSION['acc_id'];
$acc_name = $_SESSION['acc_name'] ?? 'User';
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

// GET: Fetch all messages
if ($action === 'fetch' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM tbl_chat_messages ORDER BY msg_date ASC";
    $result = mysqli_query($conn, $sql);
    
    $messages = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'msg_id' => $row['msg_id'],
                'msg_text' => htmlspecialchars($row['msg_text']),
                'msg_date' => date('m/d/Y H:i', strtotime($row['msg_date'])),
                'sender_name' => htmlspecialchars($row['sender_name']),
                'is_admin_reply' => (int)$row['is_admin_reply'],
                'timestamp' => strtotime($row['msg_date'])
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'messages' => $messages,
        'current_user' => $acc_name,
        'current_acc_id' => $acc_id
    ]);
    exit;
}

// POST: Send a new message
if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg_text = mysqli_real_escape_string($conn, $_POST['message'] ?? '');
    
    if (empty($msg_text)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit;
    }
    
    if (strlen($msg_text) > 5000) {
        echo json_encode(['success' => false, 'message' => 'Message too long (max 5000 characters)']);
        exit;
    }
    
    $sql = "INSERT INTO tbl_chat_messages (acc_id, msg_text, sender_name, is_admin_reply) 
            VALUES ($acc_id, '$msg_text', '$acc_name', 0)";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully',
            'msg_id' => mysqli_insert_id($conn)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message: ' . mysqli_error($conn)]);
    }
    exit;
}

// POST: Admin reply to a message
if ($action === 'admin_reply' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is admin
    if (!isset($_SESSION['acc_role']) || $_SESSION['acc_role'] !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Only admins can send replies']);
        exit;
    }
    
    // Get the user's account ID that admin is replying to
    $user_acc_id = (int)($_POST['user_acc_id'] ?? 0);
    if (!$user_acc_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid user account ID']);
        exit;
    }
    
    $msg_text = mysqli_real_escape_string($conn, $_POST['message'] ?? '');
    $admin_name = 'LifeBand Support';
    
    if (empty($msg_text)) {
        echo json_encode(['success' => false, 'message' => 'Reply cannot be empty']);
        exit;
    }
    
    // Insert reply under the user's account ID (not the admin's)
    $sql = "INSERT INTO tbl_chat_messages (acc_id, msg_text, sender_name, is_admin_reply, admin_id) 
            VALUES ($user_acc_id, '$msg_text', '$admin_name', 1, " . $_SESSION['acc_id'] . ")";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            'success' => true,
            'message' => 'Reply sent successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending reply']);
    }
    exit;
}

// GET: Admin fetch all chat threads
if ($action === 'admin_fetch_chats' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if user is admin
    if (!isset($_SESSION['acc_role']) || $_SESSION['acc_role'] !== 'Admin') {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    $sql = "SELECT DISTINCT cm1.acc_id, 
                   (SELECT sender_name FROM tbl_chat_messages WHERE acc_id = cm1.acc_id ORDER BY msg_date DESC LIMIT 1) as last_sender,
                   (SELECT msg_text FROM tbl_chat_messages WHERE acc_id = cm1.acc_id ORDER BY msg_date DESC LIMIT 1) as last_message,
                   (SELECT msg_date FROM tbl_chat_messages WHERE acc_id = cm1.acc_id ORDER BY msg_date DESC LIMIT 1) as last_date
            FROM tbl_chat_messages cm1
            ORDER BY last_date DESC";
    
    $result = mysqli_query($conn, $sql);
    $chats = [];
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $chats[] = [
                'acc_id' => $row['acc_id'],
                'sender_name' => htmlspecialchars($row['last_sender']),
                'last_message' => htmlspecialchars(substr($row['last_message'], 0, 50)) . (strlen($row['last_message']) > 50 ? '...' : ''),
                'last_date' => date('m/d/Y H:i', strtotime($row['last_date']))
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'chats' => $chats
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
