<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeBand - System Status & Updates</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="System_Status.css">
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
            <img src="checkwhite-removebg-preview.png" alt="System Status & Updates" class="header-icon">
            <h1>System Status & Updates</h1>
        </div>
        <div class="header-controls">
            <button class="back-btn">⟲</button>
            <button class="close-btn">✕</button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="status-container">
            <?php
            // Get the most recent status entry from tbl_system_status
            $sql = "SELECT * FROM tbl_system_status WHERE status_date IS NOT NULL ORDER BY status_date DESC, status_id DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            
            if ($result && mysqli_num_rows($result) > 0) {
                $latestStatus = mysqli_fetch_assoc($result);
                $status = strtolower($latestStatus['system_status']);
                $statusLabel = ucfirst($status);
                $isOperational = ($status === 'working' || $status === 'operational');
            } else {
                $isOperational = true;
                $statusLabel = 'Operational';
            }
            
            $statusClass = $isOperational ? 'success' : 'error';
            $statusIcon = $isOperational ? 'GreenCheck.png' : 'RedCross.png';
            ?>
            
            <div class="status-box">
                <div class="status-indicator <?php echo $statusClass; ?>">
                    <img src="<?php echo $statusIcon; ?>" alt="Status Icon" class="status-icon-img">
                </div>
                <div class="status-content">
                    <h3><?php echo $isOperational ? 'All Systems Operational' : 'System Currently Down'; ?></h3>
                    <p><?php echo $isOperational ? 'All system services are currently operational. No issues at this time.' : 'We are currently experiencing technical difficulties. Our team is working to resolve this.'; ?></p>
                </div>
            </div>

            <div class="updates-section">
                <h3>Recent Updates</h3>
                <?php
                $sql = "SELECT * FROM tbl_system_status WHERE status_date IS NOT NULL ORDER BY status_date DESC, status_id DESC";
                $result = mysqli_query($conn, $sql);
                
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($update = mysqli_fetch_assoc($result)) {
                        $status = strtolower($update['system_status']);
                        $isWorking = ($status === 'working' || $status === 'operational');
                        $icon = $isWorking ? 'GreenCheck.png' : 'RedCross.png';
                        $statusLabel = ucfirst($status);
                        $date = isset($update['status_date']) ? date('m/d/Y', strtotime($update['status_date'])) : date('m/d/Y');
                ?>
                <div class="update-item">
                    <div class="update-status-indicator <?php echo $isWorking ? 'working' : 'down'; ?>">
                        <img src="<?php echo $icon; ?>" alt="Status" class="update-status-icon">
                    </div>
                    <div class="update-date"><?php echo $date; ?></div>
                    <div class="update-details">
                        <h4><?php echo htmlspecialchars($update['system_title']); ?> - <span class="status-label"><?php echo $statusLabel; ?></span></h4>
                        <p><?php echo htmlspecialchars($update['system_description']); ?></p>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<p style="padding: 20px; color: #999;">No status updates available.</p>';
                }
                ?>
            </div>
        </div>
    </main>

    <script>
        // Function to show/hide admin link based on role
        function updateAdminLink() {
            const userRole = localStorage.getItem('userRole');
            const adminPanelLink = document.getElementById('adminPanelLink');
            if (adminPanelLink) {
                if (userRole && userRole.toLowerCase() === 'admin') {
                    adminPanelLink.style.display = 'block';
                } else {
                    adminPanelLink.style.display = 'none';
                }
            }
        }

        // Check if user is logged in from localStorage
        const userName = localStorage.getItem('userName');
        if (userName) {
            document.getElementById('userName').textContent = userName;
            document.getElementById('userProfile').style.display = 'flex';
            document.querySelector('.signin-btn').style.display = 'none';
            updateAdminLink();
        }

        // Check for URL parameters (fresh login)
        const urlParams = new URLSearchParams(window.location.search);
        const user = urlParams.get('user');
        if (user) {
            localStorage.setItem('userName', user);
            localStorage.setItem('userEmail', urlParams.get('email') || '');
            localStorage.setItem('userRegion', urlParams.get('region') || '');
            localStorage.setItem('userDOB', urlParams.get('dob') || '');
            localStorage.setItem('userRole', urlParams.get('role') || '');
            window.history.replaceState(null, null, window.location.pathname);
            // Show the profile
            document.getElementById('userName').textContent = user;
            document.getElementById('userProfile').style.display = 'flex';
            document.querySelector('.signin-btn').style.display = 'none';
            updateAdminLink();
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
    </script>
</body>
</html>
