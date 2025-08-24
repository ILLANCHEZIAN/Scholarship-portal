<?php
include '../includes/config.php';
if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get application details
$query = "SELECT a.*, u.username, u.email, s.name as scholarship_name 
          FROM applications a
          JOIN users u ON a.user_id = u.id
          JOIN scholarships s ON a.scholarship_id = s.id
          WHERE a.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Application not found");
}

$application = $result->fetch_assoc();

// Function to generate document link
function documentLink($path, $label) {
    if (empty($path)) return "Not submitted";
    $file_path = "../uploads/" . $path;
    return '<a href="' . htmlspecialchars($file_path) . '" target="_blank" class="text-decoration-none">' . htmlspecialchars($label) . '</a>';
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Application Details</h2>
        <a href="manage_applications.php" class="btn btn-secondary">Back to Applications</a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Scholarship: <?= htmlspecialchars($application['scholarship_name']) ?></h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Applicant Information</h5>
                    <p><strong>Name:</strong> <?= htmlspecialchars($application['username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($application['email']) ?></p>
                    <p><strong>Mobile:</strong> <?= htmlspecialchars($application['mobile']) ?></p>
                    <p><strong>Father's Name:</strong> <?= htmlspecialchars($application['father_name']) ?></p>
                    <p><strong>Mother's Name:</strong> <?= htmlspecialchars($application['mother_name']) ?></p>
                    <p><strong>Date of Birth:</strong> <?= date('M d, Y', strtotime($application['dob'])) ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Address</h5>
                    <p><?= nl2br(htmlspecialchars($application['address'])) ?></p>
                </div>
            </div>

            <div class="mb-4">
                <h5>Essay</h5>
                <div class="border p-3 bg-light">
                    <?= !empty($application['application_text']) ? nl2br(htmlspecialchars($application['application_text'])) : 'No essay submitted' ?>
                </div>
            </div>

            <!-- In admin/view_application.php or similar -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h4>Bank Details</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Bank Name:</strong> <?= htmlspecialchars($application['bank_name']) ?></p>
                <p><strong>Account Holder:</strong> <?= htmlspecialchars($application['account_holder_name']) ?></p>
                <p><strong>Account Number:</strong> <?= htmlspecialchars($application['account_number']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>IFSC Code:</strong> <?= htmlspecialchars($application['ifsc_code']) ?></p>
                <p><strong>Branch:</strong> <?= htmlspecialchars($application['branch']) ?></p>
                <p><strong>Passbook:</strong> 
                    <a href="../uploads/<?= htmlspecialchars($application['bank_passbook_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        View Passbook
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

            <div class="mb-4">
                <h5>Uploaded Documents</h5>
                <div class="list-group">
                    <div class="list-group-item"><?= documentLink($application['community_cert_path'], 'Community Certificate') ?></div>
                    <div class="list-group-item"><?= documentLink($application['income_cert_path'], 'Income Certificate') ?></div>
                    <div class="list-group-item"><?= documentLink($application['marksheet_10th_path'], '10th Marksheet') ?></div>
                    <div class="list-group-item"><?= documentLink($application['marksheet_12th_path'], '12th Marksheet') ?></div>
                    <div class="list-group-item"><?= documentLink($application['recent_marksheet_path'], 'Recent Marksheet') ?></div>
                    <div class="list-group-item"><?= documentLink($application['photo_path'], 'Photo') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>