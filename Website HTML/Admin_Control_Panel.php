<?php
session_start();
if (!isset($_SESSION['acc_role']) || $_SESSION['acc_role'] != 'Admin') {
    header('Location: index.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeBand - Admin Control Panel</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="Admin_Control_Panel.css">
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
            <li id="adminPanelLink" style="display:none;"><a href="Admin_Control_Panel.php" class="nav-link active">Admin Panel</a></li>
        </ul>
        <div class="nav-right">
            <div id="userProfile" class="user-profile-wrapper" style="display:none;">
                <img id="profilePic" src="profile_pic.jpg" alt="Profile" class="profile-pic-nav">
                <span id="userName" class="user-name-text"></span>
            </div>
            <button class="signin-btn">Sign in</button>
            <button class="hamburger-menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    <!-- User Profile Modal -->
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

    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <h1>ADMIN CONTROL PANEL</h1>
        </div>
        <div class="header-controls">
            <a href="Admin_Chat.php" class="chat-link-btn" title="View Support Chats">💬 Chat Support</a>
            <a href="Admin_Feedbacks.php" class="feedbacks-link-btn" title="View Feedbacks">📋 Feedbacks</a>
            <button class="back-btn">⟲</button>
            <button class="close-btn">✕</button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="admin-container">
            <!-- Add System Alert Section -->
            <div class="admin-card">
                <h2>Add System Alert</h2>
                <form id="addAlertForm" action="admin.php" method="post">
                    <input type="text" id="alertTitle" name="alerts_title" placeholder="Alert title" required>
                    <textarea id="alertMessage" name="alerts_detail" placeholder="Alert message goes here" required></textarea>
                    
                    <div class="form-group">
                        <label for="severityLevel">Severity Level</label>
                        <select id="severityLevel" name="alerts_severity" required>
                            <option value="">Select Severity</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="add_alert" class="btn btn-primary">+ Add Alert</button>
                </form>
            </div>

            <!-- Delete System Alert Section -->
            <div class="admin-card">
                <h2>Delete System Alert</h2>
                <div id="alertsList" class="alerts-list">
                    <?php
                    include 'config.php';
                    $sql = "SELECT * FROM tbl_system_alerts ORDER BY alerts_id DESC";
                    $result = mysqli_query($conn, $sql);
                    while ($alert = mysqli_fetch_assoc($result)) {
                    ?>
                    <form method="post" action="admin.php" style="display:inline;">
                        <input type="hidden" name="alerts_id" value="<?php echo $alert['alerts_id']; ?>">
                        <div class="alert-item">
                            <span><?php echo htmlspecialchars($alert['alerts_title']); ?></span>
                            <button type="submit" name="delete_alert" class="btn-close">×</button>
                        </div>
                    </form>
                    <?php } ?>
                </div>
            </div>

            <!-- Update System Status Section -->
            <div class="admin-card">
                <h2>Update System Status</h2>
                <form id="updateStatusForm" action="admin.php" method="post">
                    <input type="text" id="statusTitle" name="system_title" placeholder="Status title" required>
                    <textarea id="statusDescription" name="system_description" placeholder="Status description goes here" required></textarea>
                    
                    <div class="form-group status-toggle-group">
                        <label for="systemStatusToggle">System Status</label>
                        <div class="toggle-container">
                            <span class="toggle-label down-label">System Down</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="systemStatusToggle" name="system_status_toggle">
                                <span class="slider"></span>
                            </label>
                            <span class="toggle-label working-label">System Working</span>
                            <input type="hidden" id="systemStatusHidden" name="system_status" value="down">
                        </div>
                    </div>
                    
                    <input type="hidden" id="statusDate" name="status_date">
                    <input type="hidden" id="statusId" name="status_id" value="6">
                    
                    <button type="submit" name="add_status" class="btn btn-primary">+ Add Status Update</button>
                </form>
            </div>
        </div>
    </main>

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
            window.history.replaceState(null, null, window.location.pathname);
            // Show the profile
            document.getElementById('userName').textContent = user;
            document.getElementById('userProfile').style.display = 'flex';
            document.querySelector('.signin-btn').style.display = 'none';
        }

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

        // Sign in button
        document.querySelector('.signin-btn').addEventListener('click', function() {
            window.location.href = 'index.html';
        });

        // Back button
        document.querySelector('.back-btn').addEventListener('click', function() {
            window.history.back();
        });

        // Close button
        document.querySelector('.close-btn').addEventListener('click', function() {
            window.location.href = 'Homepage.html';
        });

        // Hamburger menu toggle
        document.querySelector('.hamburger-menu').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Add Alert Form
        document.getElementById('addAlertForm').addEventListener('submit', function(e) {
            // Submit to PHP
            this.submit();
        });

        // System Status Toggle Switch - Set current date and handle toggle
        function setCurrentDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            document.getElementById('statusDate').value = formattedDate;
        }

        // Set current date on page load
        setCurrentDate();

        // Handle toggle switch
        const statusToggle = document.getElementById('systemStatusToggle');
        const statusHidden = document.getElementById('systemStatusHidden');

        statusToggle.addEventListener('change', function() {
            if (this.checked) {
                statusHidden.value = 'working';
            } else {
                statusHidden.value = 'down';
            }
        });

        // Update Status button
        document.querySelector('.btn-success').addEventListener('click', function() {
            document.getElementById('updateStatusForm').submit();
        });
    </script>
</body>
</html>
