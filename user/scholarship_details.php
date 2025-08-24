<?php
include '../includes/config.php';
if (!isUser()) {
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM scholarships WHERE id = $id";
$result = mysqli_query($conn, $query);
$scholarship = mysqli_fetch_assoc($result);

if (!$scholarship) {
    header("Location: available_scholarships.php");
    exit();
}

// Safely get eligibility criteria with fallback to empty array
$genders = isset($scholarship['eligible_genders']) ? explode(',', $scholarship['eligible_genders']) : [];
$castes = isset($scholarship['eligible_castes']) ? explode(',', $scholarship['eligible_castes']) : [];
$degrees = isset($scholarship['eligible_degrees']) ? explode(',', $scholarship['eligible_degrees']) : [];

include '../includes/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3><?= htmlspecialchars($scholarship['name']) ?></h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Amount:</strong> ₹<?= number_format($scholarship['amount'], 2) ?></p>
                    <p><strong>Deadline:</strong> <?= date('M d, Y', strtotime($scholarship['deadline'])) ?></p>
                    <p><strong>Organization:</strong> <?= htmlspecialchars($scholarship['organization'] ?? 'N/A') ?></p>
                </div>
                <div class="col-md-6">
                    <div class="eligibility-section">
                        <h5>Eligibility Criteria</h5>
                        
                        <!-- Gender -->
                        <div class="mb-2">
                            <small class="text-muted">Gender:</small>
                            <?php if (!empty($genders)): ?>
                                <?php foreach ($genders as $gender): ?>
                                    <span class="badge bg-primary me-1"><?= htmlspecialchars(trim($gender)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Not specified</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Caste -->
                        <div class="mb-2">
                            <small class="text-muted">Caste:</small>
                            <?php if (!empty($castes)): ?>
                                <?php foreach ($castes as $caste): ?>
                                    <span class="badge bg-secondary me-1"><?= htmlspecialchars(trim($caste)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Not specified</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Degree -->
                        <div class="mb-2">
                            <small class="text-muted">Degree Level:</small>
                            <?php if (!empty($degrees)): ?>
                                <?php foreach ($degrees as $degree): ?>
                                    <span class="badge bg-info me-1"><?= htmlspecialchars(trim($degree)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Not specified</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>Description</h5>
                <p><?= nl2br(htmlspecialchars($scholarship['description'])) ?></p>
            </div>
            
            <?php if (!empty($scholarship['benefits'])): ?>
            <div class="mt-4">
                <h5>Benefits</h5>
                <p><?= nl2br(htmlspecialchars($scholarship['benefits'])) ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($scholarship['requirements'])): ?>
            <div class="mt-4">
                <h5>Requirements</h5>
                <p><?= nl2br(htmlspecialchars($scholarship['requirements'])) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="apply_scholarship.php?id=<?= $scholarship['id'] ?>" class="btn btn-primary">Apply Now</a>
                <a href="view_scholarships.php" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>