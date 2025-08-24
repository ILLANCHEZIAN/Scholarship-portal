<?php
include '../includes/config.php';
if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

$message_id = intval($_GET['id'] ?? 0);
$message = [];

// Fetch message details
$query = "SELECT * FROM contact_messages WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $message_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$message = mysqli_fetch_assoc($result);

if (!$message) {
    header("Location: dashboard.php?error=message_not_found");
    exit();
}

// Mark as read if unread
if ($message['status'] == 'unread') {
    $update_query = "UPDATE contact_messages SET status = 'read' WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, 'i', $message_id);
    mysqli_stmt_execute($update_stmt);
}

// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_reply'])) {
    $reply = trim($_POST['reply']);
    
    if (!empty($reply)) {
        $update_query = "UPDATE contact_messages SET status = 'replied', admin_reply = ?, replied_at = NOW() WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, 'si', $reply, $message_id);
        
        if (mysqli_stmt_execute($update_stmt)) {
            // Send email with reply
            $to = $message['email'];
            $subject = "Re: " . $message['subject'];
            $headers = "From: contact@scholarship.com\r\n";
            $headers .= "Reply-To: contact@scholarship.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            
            $email_body = "
                <h3>Dear {$message['name']},</h3>
                <p>Thank you for contacting us. Here is our response to your message:</p>
                <div style='background:#f5f5f5; padding:15px; border-left:3px solid #007bff; margin:10px 0;'>
                    {$reply}
                </div>
                <p>Original message:</p>
                <div style='background:#f5f5f5; padding:15px; border-left:3px solid #6c757d; margin:10px 0;'>
                    {$message['message']}
                </div>
                <p>Best regards,<br>Scholarship Portal Team</p>
            ";
            
            mail($to, $subject, $email_body, $headers);
            
            header("Location: view_message.php?id=$message_id&success=replied");
            exit();
        }
    }
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <h2 class="mt-4">View Contact Message</h2>
    
    <?php if(isset($_GET['success']) && $_GET['success'] == 'replied'): ?>
        <div class="alert alert-success">Reply sent successfully!</div>
    <?php endif; ?>
    
    <div class="card mt-4">
        <div class="card-header">
            <h4><?php echo htmlspecialchars($message['subject']); ?></h4>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?php 
                            echo $message['status'] == 'replied' ? 'success' : 
                                 ($message['status'] == 'read' ? 'primary' : 'warning'); 
                        ?>">
                            <?php echo ucfirst($message['status']); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="mb-4">
                <h5>Message:</h5>
                <div class="p-3 bg-light rounded">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
            </div>
            
            <?php if ($message['attachment_path']): ?>
            <div class="mb-4">
                <h5>Attachment:</h5>
                <a href="<?php echo $message['attachment_path']; ?>" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-download"></i> Download Attachment
                </a>
            </div>
            <?php endif; ?>
            
            <?php if ($message['admin_reply']): ?>
            <div class="mb-4">
                <h5>Your Reply (sent on <?php echo date('M d, Y H:i', strtotime($message['replied_at'])); ?>):</h5>
                <div class="p-3 bg-light rounded">
                    <?php echo nl2br(htmlspecialchars($message['admin_reply'])); ?>
                </div>
            </div>
            <?php else: ?>
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5>Reply to Message</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="reply" class="form-label">Your Reply <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reply" name="reply" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="send_reply" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Reply
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <a href="dashboard.php" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<?php include '../includes/footer.php'; ?>