<?php
include '../includes/config.php';

// Check authentication first
if (!isUser()) {
    header("Location: ../login.php?type=user");
    exit();
}

// Initialize variables
$message = '';
$application_submitted = false;
$errors = [];

// Validate scholarship ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: scholarship_list.php");
    exit();
}

$scholarship_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Check if scholarship exists
$scholarship = [];
$stmt = $conn->prepare("SELECT * FROM scholarships WHERE id = ?");
$stmt->bind_param("i", $scholarship_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: scholarship_list.php");
    exit();
}
$scholarship = $result->fetch_assoc();

// Check if already applied
$check_stmt = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND scholarship_id = ?");
$check_stmt->bind_param("ii", $user_id, $scholarship_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $message = "You have already applied for this scholarship.";
} elseif (isset($_POST['apply'])) {
    // Handle form submission
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = "../uploads/";

    // Process file uploads with error handling
    $uploads = [];
    $required_files = [
        'community_cert', 
        'income_cert', 
        'marksheet_10th', 
        'marksheet_12th', 
        'recent_marksheet', 
        'photo',
        'bank_passbook'
    ];

    foreach ($required_files as $file) {
        if (empty($_FILES[$file]['name'])) {
            $errors[] = "$file is required";
            continue;
        }
        
        $uploadResult = uploadFile($file, $upload_dir, $allowed_types, $max_size);
        if ($uploadResult['error']) {
            $errors[] = $uploadResult['error'];
        } else {
            $uploads[$file] = $uploadResult['filename'];
        }
    }

    // If no errors, process form
    if (empty($errors)) {
$stmt = $conn->prepare("INSERT INTO applications (
    user_id, scholarship_id, father_name, mother_name, address, 
    dob, mobile, application_text, community_cert_path, 
    income_cert_path, marksheet_10th_path, marksheet_12th_path, 
    recent_marksheet_path, photo_path, bank_passbook_path,
    bank_name, account_holder_name, account_number, ifsc_code, branch
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("iissssssssssssssssss", 
    $user_id,
    $scholarship_id,
    $_POST['father_name'],
    $_POST['mother_name'],
    $_POST['address'],
    $_POST['dob'],
    $_POST['mobile'],
    $_POST['essay'],
    $uploads['community_cert'],
    $uploads['income_cert'],
    $uploads['marksheet_10th'],
    $uploads['marksheet_12th'],
    $uploads['recent_marksheet'],
    $uploads['photo'],
    $uploads['bank_passbook'],
    $_POST['bank_name'],
    $_POST['account_holder_name'],
    $_POST['account_number'],
    $_POST['ifsc_code'],
    $_POST['branch']
);


        if ($stmt->execute()) {
            $application_submitted = true;
            $message = "Application submitted successfully!";
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}

// File upload function with improved error handling
function uploadFile($field, $dir, $allowed_types, $max_size) {
    $result = ['filename' => '', 'error' => ''];
    
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] == UPLOAD_ERR_NO_FILE) {
        $result['error'] = "File $field is required";
        return $result;
    }

    $file = $_FILES[$field];
    
    // Validate upload error
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = "File upload error: " . $file['error'];
        return $result;
    }

    // Validate file type
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_types)) {
        $result['error'] = "Invalid file type for $field. Allowed types: " . implode(', ', $allowed_types);
        return $result;
    }

    // Validate file size
    if ($file['size'] > $max_size) {
        $result['error'] = "File $field is too large. Max size: " . ($max_size / 1024 / 1024) . "MB";
        return $result;
    }

    // Generate unique filename
    $filename = uniqid() . '_' . $field . '.' . $ext;
    $destination = $dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $result['error'] = "Failed to save $field file";
        return $result;
    }

    $result['filename'] = $filename;
    return $result;
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Apply for Scholarship: <?= htmlspecialchars($scholarship['name']) ?></h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h4>Errors found:</h4>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($message): ?>
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle"></i> <?= $message ?></h4>
                            <?php if ($application_submitted): ?>
                                <div class="mt-4">
                                    <a href="dashboard.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-tachometer-alt me-2"></i> Back to Dashboard
                                    </a>
                                    <a href="my_applications.php" class="btn btn-outline-primary btn-lg ms-2">
                                        <i class="fas fa-file-alt me-2"></i> View My Applications
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!$application_submitted && $check_result->num_rows === 0): ?>
                        <!-- Show form only if not submitted and not already applied -->
                        <form method="POST" enctype="multipart/form-data">
                            <h4 class="mb-4">Personal Information</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="father_name" class="form-label">Father's Name</label>
                                    <input type="text" class="form-control" name="father_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mother_name" class="form-label">Mother's Name</label>
                                    <input type="text" class="form-control" name="mother_name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" name="dob" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label">Mobile Number</label>
                                    <input type="text" class="form-control" name="mobile" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="essay" class="form-label">Why do you deserve this scholarship?</label>
                                <textarea class="form-control" name="essay" rows="4" required></textarea>
                            </div>

                            <h4 class="mb-4 mt-5">Bank Details</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="account_holder_name" class="form-label">Account Holder Name</label>
                                    <input type="text" class="form-control" name="account_holder_name" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" class="form-control" name="account_number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ifsc_code" class="form-label">IFSC Code</label>
                                    <input type="text" class="form-control" name="ifsc_code" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="branch" class="form-label">Branch</label>
                                <input type="text" class="form-control" name="branch" required>
                            </div>
                            <div class="mb-3">
                                <label for="bank_passbook" class="form-label">Bank Passbook (First Page with Account Details)</label>
                                <input type="file" class="form-control" name="bank_passbook" required>
                                <small class="text-muted">Upload clear copy of first page showing account holder name and account number</small>
                            </div>

                            <h4 class="mb-4 mt-5">Required Documents</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="community_cert" class="form-label">Community Certificate (PDF/JPG)</label>
                                    <input type="file" class="form-control" name="community_cert" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="income_cert" class="form-label">Income Certificate (PDF/JPG)</label>
                                    <input type="file" class="form-control" name="income_cert" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="marksheet_10th" class="form-label">10th Marksheet</label>
                                    <input type="file" class="form-control" name="marksheet_10th" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="marksheet_12th" class="form-label">12th Marksheet</label>
                                    <input type="file" class="form-control" name="marksheet_12th" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="recent_marksheet" class="form-label">Recent Marksheet</label>
                                    <input type="file" class="form-control" name="recent_marksheet" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="photo" class="form-label">Passport Size Photo</label>
                                    <input type="file" class="form-control" name="photo" required>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" name="apply" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Application
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>