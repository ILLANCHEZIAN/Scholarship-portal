<?php
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get application ID from URL
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch application details with scholarship amount
$query = "SELECT a.*, s.name as scholarship_name, s.description, s.deadline, s.amount,
                 u.username, u.email as user_email
          FROM applications a
          JOIN scholarships s ON a.scholarship_id = s.id
          JOIN users u ON a.user_id = u.id
          WHERE a.id = ? 
          AND (a.user_id = ? OR ? = (SELECT id FROM users WHERE username = 'admin' LIMIT 1))";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $application_id, $_SESSION['user_id'], $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: my_applications.php?error=not_found");
    exit();
}

$application = mysqli_fetch_assoc($result);

// Format dates
$applied_date = date('F j, Y', strtotime($application['applied_at']));
$deadline_date = date('F j, Y', strtotime($application['deadline']));
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="my_applications.php">My Applications</a></li>
            <li class="breadcrumb-item active" aria-current="page">Application Details</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Application Details</h3>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Scholarship Information</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Scholarship Name:</label>
                        <p><?php echo htmlspecialchars($application['scholarship_name']); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description:</label>
                        <p><?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deadline:</label>
                        <p><?php echo $deadline_date; ?></p>
                    </div>
                    <?php if ($application['status'] == 'approved' && !empty($application['amount'])): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Scholarship Amount:</label>
                        <p><?php echo '$' . number_format($application['amount'], 2); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h5>Application Status</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status:</label>
                        <span class="badge bg-<?php 
                            echo $application['status'] == 'approved' ? 'success' : 
                                 ($application['status'] == 'rejected' ? 'danger' : 'warning'); 
                        ?>">
                            <?php echo ucfirst($application['status']); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Applied On:</label>
                        <p><?php echo $applied_date; ?></p>
                    </div>
                    <?php if ($application['status'] == 'approved'): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount Credited:</label>
                        <p class="text-success">$<?php echo number_format($application['amount'], 2); ?> has been credited to your account.</p>
                    </div>
                    <?php endif; ?>
                    <?php if ($application['status'] == 'rejected' && !empty($application['feedback'])): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Feedback:</label>
                        <p class="text-danger"><?php echo nl2br(htmlspecialchars($application['feedback'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Your Application</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($application['application_text'])): ?>
                        <p><?php echo nl2br(htmlspecialchars($application['application_text'])); ?></p>
                    <?php else: ?>
                        <div class="alert alert-info">No application text submitted.</div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($application['status'] == 'pending'): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> Your application is under review. 
                    You can <a href="edit_application.php?id=<?php echo $application['id']; ?>" class="alert-link">edit your application</a> 
                    until the scholarship deadline.
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Applications
                </a>
                <?php if ($application['status'] == 'pending'): ?>
                    <a href="edit_application.php?id=<?php echo $application['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Application
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
