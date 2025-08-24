<?php
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get application ID from URL
$application_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch application details to verify ownership and status
$query = "SELECT a.*, s.name as scholarship_name, s.description, s.deadline 
          FROM applications a
          JOIN scholarships s ON a.scholarship_id = s.id
          WHERE a.id = ? AND a.user_id = ? AND a.status = 'pending' AND s.deadline > NOW()";
          
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $application_id, $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: my_applications.php?error=edit_not_allowed");
    exit();
}

$application = mysqli_fetch_assoc($result);

// Define required documents
$required_documents = [
    'community_cert' => 'Community Certificate (PDF/JPG)',
    'income_cert' => 'Income Certificate (PDF/JPG)',
    'tenth_marksheet' => '10th Marksheet',
    'twelfth_marksheet' => '12th Marksheet', 
    'recent_marksheet' => 'Recent Marksheet',
    'photo' => 'Photo'
];

// Fetch existing documents for this application
$documents_query = "SELECT id, document_type, document_name, file_path FROM application_documents WHERE application_id = ?";
$stmt = mysqli_prepare($conn, $documents_query);
mysqli_stmt_bind_param($stmt, "i", $application_id);
mysqli_stmt_execute($stmt);
$documents_result = mysqli_stmt_get_result($stmt);

$existing_documents = [];
while ($row = mysqli_fetch_assoc($documents_result)) {
    $existing_documents[$row['document_type']] = $row;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_text = isset($_POST['application_text']) ? mysqli_real_escape_string($conn, $_POST['application_text']) : '';
    
    // File upload configuration
    $upload_dir = "../uploads/applications/";
    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Update application text
        if (!empty($application_text)) {
            $update_query = "UPDATE applications SET application_text = ?, applied_at = NOW() WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "si", $application_text, $application_id);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update application text.");
            }
        }
        
        // Handle document deletions
        if (!empty($_POST['delete_documents'])) {
            foreach ($_POST['delete_documents'] as $doc_id) {
                $doc_id = intval($doc_id);
                
                // Get file path before deletion
                $get_path_query = "SELECT file_path FROM application_documents WHERE id = ? AND application_id = ?";
                $stmt = mysqli_prepare($conn, $get_path_query);
                mysqli_stmt_bind_param($stmt, "ii", $doc_id, $application_id);
                mysqli_stmt_execute($stmt);
                $path_result = mysqli_stmt_get_result($stmt);
                
                if ($doc = mysqli_fetch_assoc($path_result)) {
                    // Delete file from server
                    if (file_exists($doc['file_path'])) {
                        unlink($doc['file_path']);
                    }
                    
                    // Delete record from database
                    $delete_query = "DELETE FROM application_documents WHERE id = ? AND application_id = ?";
                    $stmt = mysqli_prepare($conn, $delete_query);
                    mysqli_stmt_bind_param($stmt, "ii", $doc_id, $application_id);
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to delete document.");
                    }
                }
            }
        }
        
        // Handle document uploads for each required type
        foreach ($required_documents as $doc_type => $doc_label) {
            if (!empty($_FILES[$doc_type]['name'])) {
                $file_name = $_FILES[$doc_type]['name'];
                $file_size = $_FILES[$doc_type]['size'];
                $file_tmp = $_FILES[$doc_type]['tmp_name'];
                $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Validate file
                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("Invalid file type for $doc_label. Only PDF, JPG, JPEG, PNG are allowed.");
                }
                
                if ($file_size > $max_size) {
                    throw new Exception("File too large for $doc_label. Maximum size is 5MB.");
                }
                
                // Delete existing document of this type if it exists
                if (isset($existing_documents[$doc_type])) {
                    if (file_exists($existing_documents[$doc_type]['file_path'])) {
                        unlink($existing_documents[$doc_type]['file_path']);
                    }
                    $delete_query = "DELETE FROM application_documents WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $delete_query);
                    mysqli_stmt_bind_param($stmt, "i", $existing_documents[$doc_type]['id']);
                    mysqli_stmt_execute($stmt);
                }
                
                // Generate unique filename
                $new_filename = $application_id . '_' . $doc_type . '_' . uniqid() . '.' . $file_type;
                $destination = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file_tmp, $destination)) {
                    $insert_query = "INSERT INTO application_documents 
                                    (application_id, document_type, document_name, file_path) 
                                    VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $insert_query);
                    mysqli_stmt_bind_param($stmt, "isss", $application_id, $doc_type, $file_name, $destination);
                    
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Failed to save $doc_label to database.");
                    }
                } else {
                    throw new Exception("Failed to upload $doc_label.");
                }
            }
        }
        
        // Commit transaction
        mysqli_commit($conn);
        $_SESSION['success_message'] = "Application updated successfully!";
        header("Location: view_application.php?id=" . $application_id);
        exit();
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = $e->getMessage();
    }
}

// Re-fetch documents after potential updates
$stmt = mysqli_prepare($conn, $documents_query);
mysqli_stmt_bind_param($stmt, "i", $application_id);
mysqli_stmt_execute($stmt);
$documents_result = mysqli_stmt_get_result($stmt);

$existing_documents = [];
while ($row = mysqli_fetch_assoc($documents_result)) {
    $existing_documents[$row['document_type']] = $row;
}
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="my_applications.php">My Applications</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Application</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Edit Application</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            
            <div class="mb-4">
                <h5>Scholarship: <?php echo htmlspecialchars($application['scholarship_name']); ?></h5>
                <p class="text-muted">Deadline: <?php echo date('F j, Y', strtotime($application['deadline'])); ?></p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> You can edit your application and documents until the scholarship deadline.
                </div>
            </div>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="application_text" class="form-label fw-bold">Application Text *</label>
                    <textarea class="form-control" id="application_text" name="application_text" rows="10" required><?php 
                        echo isset($_POST['application_text']) ? htmlspecialchars($_POST['application_text']) : 
                            htmlspecialchars($application['application_text']); 
                    ?></textarea>
                </div>
                
                <div class="mb-4">
                    <h5 class="mb-3">Required Documents</h5>
                    
                    <div class="row g-3">
                        <?php foreach ($required_documents as $doc_type => $doc_label): ?>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><?php echo $doc_label; ?></h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (isset($existing_documents[$doc_type])): ?>
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="text-truncate" style="max-width: 180px;">
                                                        <?php echo htmlspecialchars($existing_documents[$doc_type]['document_name']); ?>
                                                    </span>
                                                    <div>
                                                        <a href="<?php echo $existing_documents[$doc_type]['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="<?php echo $existing_documents[$doc_type]['file_path']; ?>" download class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <input type="checkbox" name="delete_documents[]" value="<?php echo $existing_documents[$doc_type]['id']; ?>" class="form-check-input ms-2" title="Delete this document">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning py-2 mb-2">Not uploaded yet</div>
                                        <?php endif; ?>
                                        
                                        <div class="mt-2">
                                            <label for="<?php echo $doc_type; ?>" class="form-label small">Upload New:</label>
                                            <input type="file" class="form-control form-control-sm" id="<?php echo $doc_type; ?>" name="<?php echo $doc_type; ?>" accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="text-muted">Max 5MB (PDF, JPG, PNG)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="view_application.php?id=<?php echo $application_id; ?>" class="btn btn-secondary">
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

<?php include '../includes/footer.php'; ?>