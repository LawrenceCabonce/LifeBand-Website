<?php
session_start();
if (!isset($_SESSION['acc_role']) || $_SESSION['acc_role'] != 'Admin') {
    header('Location: index.html');
    exit;
}

include 'config.php';

// Get all feedbacks
$sql = "SELECT * FROM tbl_feedback ORDER BY fback_id DESC";
$result = mysqli_query($conn, $sql);
$feedbacks = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $feedbacks[] = $row;
    }
}

$stats_sql = "SELECT 
    COUNT(*) as total,
    AVG(fback_rating) as avg_rating,
    MAX(fback_rating) as max_rating,
    MIN(fback_rating) as min_rating
    FROM tbl_feedback";
$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LifeBand - Admin Feedbacks</title>
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="Admin_Feedbacks.css">
</head>
<body>

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

    <!-- Header Section -->
    <div class="page-header">
        <div class="header-content">
            <h1>📋 FEEDBACK MANAGEMENT</h1>
            <p>Review and analyze user feedbacks</p>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3><?php echo $stats['total'] ?? 0; ?></h3>
                <p>Total Feedbacks</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <h3><?php echo round($stats['avg_rating'] ?? 0, 1); ?></h3>
                <p>Average Rating</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📈</div>
            <div class="stat-content">
                <h3><?php echo $stats['max_rating'] ?? 0; ?></h3>
                <p>Highest Rating</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📉</div>
            <div class="stat-content">
                <h3><?php echo $stats['min_rating'] ?? 0; ?></h3>
                <p>Lowest Rating</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="feedbacks-container">
            <div class="feedbacks-header">
                <h2>User Feedbacks</h2>
                <div class="filter-options">
                    <input type="text" id="searchInput" placeholder="Search feedbacks..." class="search-box">
                    <select id="ratingFilter" class="rating-filter">
                        <option value="">All Ratings</option>
                        <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                        <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                        <option value="3">⭐⭐⭐ 3 Stars</option>
                        <option value="2">⭐⭐ 2 Stars</option>
                        <option value="1">⭐ 1 Star</option>
                    </select>
                </div>
            </div>

            <?php if (empty($feedbacks)): ?>
                <div class="no-feedbacks">
                    <p>No feedbacks yet</p>
                </div>
            <?php else: ?>
                <div class="feedbacks-list">
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="feedback-card" data-rating="<?php echo $feedback['fback_rating']; ?>">
                            <div class="feedback-header">
                                <h3><?php echo htmlspecialchars($feedback['fback_name']); ?></h3>
                                <div class="feedback-rating">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <span class="star <?php echo $i < $feedback['fback_rating'] ? 'filled' : ''; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="feedback-content">
                                <?php echo htmlspecialchars($feedback['fback_content']); ?>
                            </div>
                            <div class="feedback-footer">
                                <span class="feedback-id">ID: <?php echo $feedback['fback_id']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
            localStorage.setItem('userEmail', urlParams.get('email') || '');
            localStorage.setItem('userRegion', urlParams.get('region') || '');
            localStorage.setItem('userDOB', urlParams.get('dob') || '');
            localStorage.setItem('userRole', urlParams.get('role') || '');
            window.history.replaceState(null, null, window.location.pathname);
            // Show the profile
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

        // Search and Filter functionality
        const searchInput = document.getElementById('searchInput');
        const ratingFilter = document.getElementById('ratingFilter');
        const feedbackCards = document.querySelectorAll('.feedback-card');

        function filterFeedbacks() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedRating = ratingFilter.value;

            feedbackCards.forEach(card => {
                const name = card.querySelector('h3').textContent.toLowerCase();
                const content = card.querySelector('.feedback-content').textContent.toLowerCase();
                const rating = card.getAttribute('data-rating');

                const matchesSearch = name.includes(searchTerm) || content.includes(searchTerm);
                const matchesRating = !selectedRating || rating === selectedRating;

                card.style.display = (matchesSearch && matchesRating) ? 'block' : 'none';
            });
        }

        searchInput.addEventListener('input', filterFeedbacks);
        ratingFilter.addEventListener('change', filterFeedbacks);
    </script>
</body>
</html>
