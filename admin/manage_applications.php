<?php
include '../includes/config.php';
if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

// Update application status
if (isset($_POST['status']) && isset($_POST['app_id'])) {
    $app_id = mysqli_real_escape_string($conn, $_POST['app_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $query = "UPDATE applications SET status='$status' WHERE id=$app_id";
    mysqli_query($conn, $query);
    
    // Add feedback/notes if provided
    if (isset($_POST['feedback'])) {
        $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
        $query = "UPDATE applications SET feedback='$feedback' WHERE id=$app_id";
        mysqli_query($conn, $query);
    }
}

?>

<?php include '../includes/header.php'; ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mt-4">Manage Applications</h2>
        <a href="dashboard.php" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All Applications</h4>
                <span class="badge bg-light text-dark">
                    <?php 
                    $count_query = "SELECT COUNT(*) as total FROM applications";
                    $count_result = mysqli_query($conn, $count_query);
                    $count = mysqli_fetch_assoc($count_result);
                    echo $count['total'] . " Applications";
                    ?>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Student</th>
                            <th>Scholarship</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT a.id, u.username, s.name, a.applied_at, a.status, a.feedback 
                                  FROM applications a
                                  JOIN users u ON a.user_id = u.id
                                  JOIN scholarships s ON a.scholarship_id = s.id
                                  ORDER BY a.applied_at DESC";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= date('M d, Y', strtotime($row['applied_at'])) ?></td>
                            
                            <td>
                                <span class="badge bg-<?= 
                                    $row['status'] == 'approved' ? 'success' : 
                                    ($row['status'] == 'rejected' ? 'danger' : 'warning')
                                ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            
                            <td>
                                     <form method="POST" class="d-flex align-items-center">
        <input type="hidden" name="app_id" value="<?= $row['id'] ?>">
        <select name="status" class="form-select form-select-sm me-2" style="width: 120px;" onchange="this.form.submit()">
            <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="approved" <?= $row['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>

        <button type="button" class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#feedbackModal<?= $row['id'] ?>">
            <i class="fas fa-comment"></i>
        </button>

        <a href="view_application.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-eye"></i>
        </a>
    </form>


                                <!-- Feedback Modal -->
                                <div class="modal fade" id="feedbackModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Feedback for <?= htmlspecialchars($row['username']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="app_id" value="<?= $row['id'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                                            <option value="approved" <?= $row['status'] == 'approved' ? 'selected' : '' ?>>Approved</option>
                                                            <option value="rejected" <?= $row['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Feedback/Notes</label>
                                                        <textarea name="feedback" class="form-control" rows="4"><?= isset($row['feedback']) ? htmlspecialchars($row['feedback']) : '' ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<br>
<br>
<br>

<?php include '../includes/footer.php'; ?>