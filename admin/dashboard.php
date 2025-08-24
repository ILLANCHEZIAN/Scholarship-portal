<?php
include '../includes/config.php';
if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

// Handle message reply
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply_message'])) {
    $message_id = intval($_POST['message_id']);
    $reply = mysqli_real_escape_string($conn, $_POST['reply']);
    
    // Update message with reply and mark as replied
    $query = "UPDATE admin_messages SET 
              reply = '$reply', 
              replied_at = NOW(), 
              is_replied = 1 
              WHERE id = $message_id";
    
    if (mysqli_query($conn, $query)) {
        $success = "Reply sent successfully!";
    } else {
        $error = "Error sending reply: " . mysqli_error($conn);
    }
}

// Handle scholarship creation if form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_scholarship'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $deadline = mysqli_real_escape_string($conn, $_POST['deadline']);
    $eligibility = mysqli_real_escape_string($conn, $_POST['eligibility']);
    
    $query = "INSERT INTO scholarships (name, description, amount, deadline, eligibility) 
              VALUES ('$name', '$description', '$amount', '$deadline', '$eligibility')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: ../user/view_scholarships.php?admin_added=1");
        exit();
    } else {
        $error = "Error adding scholarship: " . mysqli_error($conn);
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="container-fluid">
    <h2 class="mt-4">Admin Dashboard</h2>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Quick Stats Row -->
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <?php
                    $user_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
                    $user_data = mysqli_fetch_assoc($user_count);
                    ?>
                    <h1 class="display-5"><?php echo $user_data['count']; ?></h1>
                    <a href="manage_users.php" class="text-white">View All</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Active Scholarships</h5>
                    <?php
                    $scholarship_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM scholarships WHERE deadline >= CURDATE()");
                    $scholarship_data = mysqli_fetch_assoc($scholarship_count);
                    ?>
                    <h1 class="display-5"><?php echo $scholarship_data['count']; ?></h1>
                    <a href="manage_scholarships.php" class="text-white">Manage</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Pending Applications</h5>
                    <?php
                    $pending_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM applications WHERE status = 'pending'");
                    $pending_data = mysqli_fetch_assoc($pending_count);
                    ?>
                    <h1 class="display-5"><?php echo $pending_data['count']; ?></h1>
                    <a href="manage_applications.php?filter=pending" class="text-white">Review</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">New Messages</h5>
                    <?php
                    $message_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM admin_messages WHERE is_replied = 0");
                    $message_data = mysqli_fetch_assoc($message_count);
                    ?>
                    <h1 class="display-5"><?php echo $message_data['count']; ?></h1>
                    <a href="#messagesSection" class="text-white">View Messages</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Cards Row -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">View, edit, or delete user accounts.</p>
                    <a href="manage_users.php" class="btn btn-primary">Go to Users</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Manage Scholarships</h5>
                    <p class="card-text">Edit or remove existing scholarships.</p>
                    <a href="manage_scholarships.php" class="btn btn-success">Manage</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Applications</h5>
                    <p class="card-text">Review and process scholarship applications.</p>
                    <a href="manage_applications.php" class="btn btn-info">View Applications</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- User Messages Section -->
    <div class="card mt-4" id="messagesSection">
        <div class="card-header bg-primary text-white">
            <h4>User Messages</h4>
        </div>
        <div class="card-body">
            <?php
            $messages_query = "SELECT m.*, u.username, u.email 
                             FROM admin_messages m
                             JOIN users u ON m.user_id = u.id
                             ORDER BY m.is_replied ASC, m.created_at DESC";
            $messages_result = mysqli_query($conn, $messages_query);
            
            if (mysqli_num_rows($messages_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Sent</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($message = mysqli_fetch_assoc($messages_result)): ?>
                            <tr class="<?php echo $message['is_replied'] ? '' : 'table-warning'; ?>">
                                <td>
                                    <?php echo htmlspecialchars($message['username']); ?><br>
                                    <small><?php echo htmlspecialchars($message['email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                                <td><?php echo date('M d, Y', strtotime($message['created_at'])); ?></td>
                                <td>
                                    <?php if($message['is_replied']): ?>
                                        <span class="badge bg-success">Replied</span><br>
                                        <small><?php echo date('M d, Y', strtotime($message['replied_at'])); ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                            data-bs-target="#replyModal<?php echo $message['id']; ?>">
                                        <?php echo $message['is_replied'] ? 'View Reply' : 'Reply'; ?>
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Reply Modal for each message -->
                            <div class="modal fade" id="replyModal<?php echo $message['id']; ?>" tabindex="-1" 
                                 aria-labelledby="replyModalLabel<?php echo $message['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="replyModalLabel<?php echo $message['id']; ?>">
                                                Reply to <?php echo htmlspecialchars($message['username']); ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></p>
                                            <p><strong>Original Message:</strong></p>
                                            <div class="border p-2 mb-3">
                                                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                            </div>
                                            
                                            <?php if($message['is_replied']): ?>
                                                <p><strong>Your Reply:</strong></p>
                                                <div class="border p-2 mb-3 bg-light">
                                                    <?php echo nl2br(htmlspecialchars($message['reply'])); ?>
                                                </div>
                                            <?php else: ?>
                                                <form method="POST">
                                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="reply<?php echo $message['id']; ?>" class="form-label">Your Reply</label>
                                                        <textarea class="form-control" id="reply<?php echo $message['id']; ?>" 
                                                                  name="reply" rows="4" required></textarea>
                                                    </div>
                                                    <button type="submit" name="reply_message" class="btn btn-primary">Send Reply</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No messages from users yet.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add Global Scholarship Section -->
    <div class="card mt-4">
        <div class="card-header bg-dark text-white">
            <h4>Add Global Scholarship</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Scholarship Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="amount" name="amount" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="deadline" name="deadline" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="eligibility" class="form-label">Eligibility Criteria <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="eligibility" name="eligibility" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" name="add_scholarship" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle"></i> Add Scholarship
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Recent Activity</h4>
        </div>
        <div class="card-body">
            <?php
            $recent_query = "SELECT a.id, u.username, s.name as scholarship, a.status, a.applied_at 
                            FROM applications a
                            JOIN users u ON a.user_id = u.id
                            JOIN scholarships s ON a.scholarship_id = s.id
                            ORDER BY a.applied_at DESC
                            LIMIT 5";
            $recent_result = mysqli_query($conn, $recent_query);
            
            if (mysqli_num_rows($recent_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Scholarship</th>
                                <th>Applied On</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($recent_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['scholarship']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['applied_at'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $row['status'] == 'approved' ? 'success' : 
                                             ($row['status'] == 'rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No recent activity found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>