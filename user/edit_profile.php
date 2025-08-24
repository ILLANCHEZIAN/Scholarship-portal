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

if (!$user) {
    $_SESSION['error'] = "User profile not found";
    header("Location: profile.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize all fields with empty string if not set
    $fields = [
        'full_name', 'phone', 'dob', 'gender', 'address', 'city', 
        'state', 'pincode', 'qualification', 'institution', 'course',
        'year_of_study', 'bank_name', 'account_number', 'ifsc_code',
        'account_holder', 'religion', 'community'
    ];
    
    $data = [];
    foreach ($fields as $field) {
        $data[$field] = !empty($_POST[$field]) ? mysqli_real_escape_string($conn, $_POST[$field]) : NULL;
    }

    $query = "UPDATE users SET 
              full_name = ?,
              phone = ?,
              dob = ?,
              gender = ?,
              address = ?,
              city = ?,
              state = ?,
              pincode = ?,
              qualification = ?,
              institution = ?,
              course = ?,
              year_of_study = ?,
              bank_name = ?,
              account_number = ?,
              ifsc_code = ?,
              account_holder = ?,
              religion = ?,
              community = ?,
              updated_at = NOW()
              WHERE id = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssssssssssssi", 
        $data['full_name'],
        $data['phone'],
        $data['dob'],
        $data['gender'],
        $data['address'],
        $data['city'],
        $data['state'],
        $data['pincode'],
        $data['qualification'],
        $data['institution'],
        $data['course'],
        $data['year_of_study'],
        $data['bank_name'],
        $data['account_number'],
        $data['ifsc_code'],
        $data['account_holder'],
        $data['religion'],
        $data['community'],
        $user_id
    );
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $error = "Error updating profile: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | Scholarship Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .fallback-nav {
            background-color: #0d6efd;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <!-- Safe navbar inclusion with robust fallback -->
    <?php
    $navbar_path = __DIR__ . '/../includes/navbar.php';
    if (file_exists($navbar_path) && is_readable($navbar_path)) {
        include $navbar_path;
    } else {
        // Comprehensive fallback navigation
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary fallback-nav">
                <div class="container">
                    <a class="navbar-brand" href="../index.php">
                        <i class="fas fa-graduation-cap"></i> Scholarship Portal
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#fallbackNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="fallbackNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
              </nav>';
    }
    ?>
    
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profile</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= htmlspecialchars($_SESSION['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <?= htmlspecialchars($error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Basic Information Section -->
                            <div id="basic" class="form-section">
                                <h4 class="mb-3"><i class="fas fa-user me-2"></i>Basic Information</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="fullName" name="full_name" 
                                                   value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" required>
                                            <label for="fullName" class="required-field">Full Name</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control" id="phone" name="phone" 
                                                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                                            <label for="phone" class="required-field">Phone Number</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="date" class="form-control" id="dob" name="dob" 
                                                   value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
                                            <label for="dob">Date of Birth</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                                <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                                <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                            </select>
                                            <label for="gender">Gender</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add other sections (Address, Education, Bank) here -->

                            <div class="d-flex justify-content-between mt-4">
                                <a href="profile.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enhanced client-side validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                let valid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        valid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                }
            });
            
            // Add input validation feedback
            form.querySelectorAll('[required]').forEach(field => {
                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
</body>
</html>