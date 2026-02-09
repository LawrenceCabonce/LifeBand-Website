<?php
session_start();
if (!isset($_SESSION['acc_role']) || $_SESSION['acc_role'] != 'Admin') {
    header('Location: index.html');
    exit;
}

include 'config.php';

// Get all chat conversations
$sql = "SELECT DISTINCT cm1.acc_id, 
               (SELECT sender_name FROM tbl_chat_messages WHERE acc_id = cm1.acc_id ORDER BY msg_date DESC LIMIT 1) as sender_name,
               (SELECT msg_text FROM tbl_chat_messages WHERE acc_id = cm1.acc_id ORDER BY msg_date DESC LIMIT 1) as last_message,
               (SELECT msg_date FROM tbl_chat_messages WHERE acc_id = cm1.acc_id ORDER BY msg_date DESC LIMIT 1) as last_date,
               (SELECT COUNT(*) FROM tbl_chat_messages WHERE acc_id = cm1.acc_id AND is_admin_reply = 0) as unread_count
        FROM tbl_chat_messages cm1
        ORDER BY last_date DESC";

$result = mysqli_query($conn, $sql);
$conversations = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $conversations[] = $row;
    }
}

// Get selected conversation
$selected_acc_id = $_GET['chat_id'] ?? null;
$selected_messages = [];

if ($selected_acc_id) {
    $selected_acc_id = (int)$selected_acc_id;
    $msg_sql = "SELECT * FROM tbl_chat_messages WHERE acc_id = $selected_acc_id ORDER BY msg_date ASC";
    $msg_result = mysqli_query($conn, $msg_sql);
    
    if ($msg_result && mysqli_num_rows($msg_result) > 0) {
        while ($msg = mysqli_fetch_assoc($msg_result)) {
            $selected_messages[] = $msg;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeBand - Admin Chat Management</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="Admin_Chat.css">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="nav-left">
            <img src="lifeband-text-logo.png" alt="LifeBand" class="logo">
        </div>
        <ul class="nav-menu">
            <li><a href="Homepage.html" class="nav-link">Home</a></li>
            <li><a href="Dev_info.html" class="nav-link">Device information</a></li>
            <li><a href="About_us.html" class="nav-link">About Us</a></li>
            <li><a href="Contact.html" class="nav-link">Contact</a></li>
            <li id="adminPanelLink" style="display:none;"><a href="Admin_Control_Panel.php" class="nav-link">Admin Panel</a></li>
        </ul>
        <div class="nav-right">
            <div id="userProfile" class="user-profile-wrapper" style="display:none;">
                <img id="profilePic" src="profile_pic.jpg" alt="Profile" class="profile-pic-nav">
                <span id="userName" class="user-name-text"></span>
            </div>
            <button class="signin-btn">Sign in</button>
            <a href="Admin_Control_Panel.php" class="back-to-panel-btn" title="Back to Admin Panel">← Back</a>
            <button class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- User Profile -->
    <div id="userProfileModal" class="profile-modal" style="display:none;">
        <div class="profile-modal-content">
            <button class="profile-modal-close">&times;</button>
            <div class="profile-modal-header">
                <img id="modalProfilePic" src="profile_pic.jpg" alt="Profile Picture" class="profile-modal-pic">
            </div>
            <div class="profile-modal-body">
                <div class="profile-info-group">
                    <label>Name</label>
                    <p id="modalUserName">-</p>
                </div>
                <div class="profile-info-group">
                    <label>Role</label>
                    <p id="modalUserRole">-</p>
                </div>
                <div class="profile-info-group">
                    <label>Email</label>
                    <p id="modalUserEmail">-</p>
                </div>
                <div class="profile-info-group">
                    <label>Region</label>
                    <p id="modalUserRegion">-</p>
                </div>
                <div class="profile-info-group">
                    <label>Date of Birth</label>
                    <p id="modalUserDOB">-</p>
                </div>
            </div>
            <div class="profile-modal-footer">
                <button class="edit-profile-btn">Edit Profile</button>
                <button class="logout-btn">Logout</button>
            </div>
        </div>
    </div>

    <div class="chat-admin-container">
        <!-- Conversations List -->
        <div class="conversations-panel">
            <div class="conversations-header">
                <h3>💬 Chat Conversations</h3>
            </div>
            <?php foreach ($conversations as $conv): ?>
                <a href="?chat_id=<?php echo $conv['acc_id']; ?>" class="conversation-item <?php echo $selected_acc_id == $conv['acc_id'] ? 'active' : ''; ?>">
                    <div class="conversation-sender">
                        <?php echo htmlspecialchars($conv['sender_name']); ?>
                        <?php if ($conv['unread_count'] > 0): ?>
                            <span class="unread-badge"><?php echo $conv['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="conversation-preview">
                        <?php echo htmlspecialchars(substr($conv['last_message'], 0, 50)); ?>...
                    </div>
                    <div class="conversation-date">
                        <?php echo date('m/d/Y H:i', strtotime($conv['last_date'])); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Chat View -->
        <div class="chat-view-panel">
            <?php if (!$selected_acc_id): ?>
                <div class="chat-view-empty">
                    <div class="empty-state">
                        <p>👈 Select a conversation to view messages</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="chat-header">
                    <h3>Chat with <?php echo htmlspecialchars($selected_messages[0]['sender_name'] ?? 'User'); ?></h3>
                </div>

                <div class="chat-messages-area">
                    <?php foreach ($selected_messages as $msg): ?>
                        <div class="message <?php echo $msg['is_admin_reply'] ? 'admin' : 'user'; ?>">
                            <div class="message-sender">
                                <?php echo htmlspecialchars($msg['sender_name']); ?>
                            </div>
                            <div class="message-text">
                                <?php echo htmlspecialchars($msg['msg_text']); ?>
                            </div>
                            <div class="message-date">
                                <?php echo date('m/d/Y H:i', strtotime($msg['msg_date'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form class="reply-form" id="replyForm">
                    <input type="hidden" id="userAccId" value="<?php echo $selected_acc_id; ?>">
                    <textarea id="replyText" placeholder="Type your reply..." required></textarea>
                    <button type="submit">Send Reply</button>
                </form>

                <script>
                    document.getElementById('replyForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        const replyText = document.getElementById('replyText').value;
                        const userAccId = document.getElementById('userAccId').value;
                        
                        if (!replyText.trim()) return;

                        const formData = new FormData();
                        formData.append('action', 'admin_reply');
                        formData.append('message', replyText);
                        formData.append('user_acc_id', userAccId);

                        fetch('support.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('replyText').value = '';
                                // Reload the page to show new message
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            } else {
                                alert('Error: ' + data.message);
                            }
                        });
                    });
                </script>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Check if user is logged in
        const userName = localStorage.getItem('userName');
        if (userName) {
            document.getElementById('userName').textContent = userName;
            document.getElementById('userProfile').style.display = 'flex';
            document.querySelector('.signin-btn').style.display = 'none';
        }

        const urlParams = new URLSearchParams(window.location.search);
        const user = urlParams.get('user');
        if (user) {
            localStorage.setItem('userName', user);
            localStorage.setItem('userEmail', urlParams.get('email') || '');
            localStorage.setItem('userRegion', urlParams.get('region') || '');
            localStorage.setItem('userDOB', urlParams.get('dob') || '');
            localStorage.setItem('userRole', urlParams.get('role') || '');
            window.history.replaceState(null, null, window.location.pathname);

            document.getElementById('userName').textContent = user;
            document.getElementById('userProfile').style.display = 'flex';
            document.querySelector('.signin-btn').style.display = 'none';
        }

        // Hamburger menu toggle
        document.querySelector('.hamburger-menu').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Sign in button
        document.querySelector('.signin-btn').addEventListener('click', function() {
            window.location.href = 'index.html';
        });

        // User Profile Modal functionality
        const userProfileWrapper = document.getElementById('userProfile');
        const userProfileModal = document.getElementById('userProfileModal');
        const profileModalClose = document.querySelector('.profile-modal-close');
        const logoutBtn = document.querySelector('.logout-btn');
        const editProfileBtn = document.querySelector('.edit-profile-btn');

        if (userProfileWrapper) {
            userProfileWrapper.addEventListener('click', function() {
                userProfileModal.style.display = 'flex';
                // Populate modal with user data
                document.getElementById('modalUserName').textContent = localStorage.getItem('userName') || '-';
                document.getElementById('modalUserRole').textContent = localStorage.getItem('userRole') || '-';
                document.getElementById('modalUserEmail').textContent = localStorage.getItem('userEmail') || '-';
                document.getElementById('modalUserRegion').textContent = localStorage.getItem('userRegion') || '-';
                document.getElementById('modalUserDOB').textContent = localStorage.getItem('userDOB') || '-';
            });
        }

        profileModalClose.addEventListener('click', function() {
            userProfileModal.style.display = 'none';
        });

        userProfileModal.addEventListener('click', function(e) {
            if (e.target === userProfileModal) {
                userProfileModal.style.display = 'none';
            }
        });

        logoutBtn.addEventListener('click', function() {
            localStorage.clear();
            window.location.href = 'index.html';
        });

        editProfileBtn.addEventListener('click', function() {
            window.location.href = 'Account_Edit.html';
        });
    </script>
</body>
</html>
