<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user details
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user not found, show error and exit
if (!$user) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Profile Error</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body>
        <div class='container mt-5'>
            <div class='alert alert-danger'>User profile not found. Please contact support.</div>
            <a href='dashboard.php' class='btn btn-primary'>Back to Dashboard</a>
        </div>
    </body>
    </html>";
    exit();
}

// Safely get profile picture path
$profile_pic = '';
if (!empty($user['profile_pic'])) {
    $profile_path = __DIR__ . '/../uploads/' . $user['profile_pic'];
    $profile_pic = (file_exists($profile_path)) ? '../uploads/' . $user['profile_pic'] : '';
}

// Fallback to avatar if no picture
if (empty($profile_pic)) {
    $profile_pic = 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . 
                   '&size=120&background=' . substr(md5($user['username']), 0, 6);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Scholarship Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .profile-pic {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-details {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .section-title {
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php 
    // Check if navbar exists before including
    $navbar_path = __DIR__ . '/../includes/navbar.php';
    if (file_exists($navbar_path)) {
        include $navbar_path;
    } else {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
                <div class="container">
                    <a class="navbar-brand" href="#">Scholarship Portal</a>
                </div>
              </nav>';
    }
    ?>
    
    <div class="container py-5">
        <div class="profile-header text-center">
            <div class="d-flex justify-content-center mb-3">
                <img src="<?= htmlspecialchars($profile_pic) ?>" 
                     alt="Profile Picture" 
                     class="profile-pic rounded-circle"
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>&size=120&background=random'">
            </div>
            <h3><?= htmlspecialchars($user['username']) ?></h3>
            <p class="mb-1"><i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($user['email'] ?? 'Not provided') ?></p>
            <a href="edit_profile.php" class="btn btn-light mt-3">
                <i class="fas fa-edit me-1"></i> Edit Profile
            </a>
        </div>

        <div class="profile-details mb-4 position-relative">
            <button class="btn btn-sm btn-outline-primary edit-btn" onclick="location.href='edit_profile.php'">
                <i class="fas fa-edit"></i> Edit
            </button>
            
            <h5 class="mb-4 section-title"><i class="fas fa-user me-2"></i>Basic Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['username'] ?? 'Not provided') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'Not provided') ?></p>
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name'] ?? 'Not provided') ?></p>
                    <p><strong>Date of Birth:</strong> <?= !empty($user['dob']) ? date('F j, Y', strtotime($user['dob'])) : 'Not provided' ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Member Since:</strong> <?= !empty($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'Not provided' ?></p>
                    <p><strong>Last Updated:</strong> <?= !empty($user['updated_at']) ? date('F j, Y', strtotime($user['updated_at'])) : 'Not provided' ?></p>
                    <p><strong>Gender:</strong> <?= htmlspecialchars($user['gender'] ?? 'Not provided') ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'Not provided') ?></p>
                </div>
            </div>
        </div>

        <!-- [Rest of your profile sections remain the same] -->

        <div class="d-flex justify-content-between">
            <a href="dashboard.php" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
            </a>
            <a href="edit_profile.php" class="btn btn-success">
                <i class="fas fa-edit me-1"></i> Edit Complete Profile
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>