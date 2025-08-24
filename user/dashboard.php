<?php
include '../includes/config.php';
if (!isUser()) {
    header("Location: ../login.php?type=user");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's active applications count
$applications_query = "SELECT COUNT(*) as total FROM applications WHERE user_id = $user_id";
$applications_result = mysqli_query($conn, $applications_query);
$applications_data = mysqli_fetch_assoc($applications_result);

// Get user's approved applications count
$approved_query = "SELECT COUNT(*) as approved FROM applications WHERE user_id = $user_id AND status = 'approved'";
$approved_result = mysqli_query($conn, $approved_query);
$approved_data = mysqli_fetch_assoc($approved_result);

// Get available scholarships count
$scholarships_query = "SELECT COUNT(*) as available FROM scholarships WHERE deadline >= CURDATE()";
$scholarships_result = mysqli_query($conn, $scholarships_query);
$scholarships_data = mysqli_fetch_assoc($scholarships_result);
?>

<?php include '../includes/header.php'; ?>

<div class="container mt-4">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>

    <a href="profile.php" class="btn btn-secondary mb-3">
        <i class="fas fa-user"></i> View Profile
    </a>
    
    <!-- Dashboard Stats Cards -->
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Available Scholarships</h5>
                    <h1 class="display-4"><?php echo $scholarships_data['available']; ?></h1>
                    <a href="view_scholarships.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">My Applications</h5>
                    <h1 class="display-4"><?php echo $applications_data['total']; ?></h1>
                    <a href="my_applications.php" class="text-white">View All <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Approved</h5>
                    <h1 class="display-4"><?php echo $approved_data['approved']; ?></h1>
                    <a href="my_applications.php?filter=approved" class="text-white">View Approved <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Applications -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Recent Applications</h4>
        </div>
        <div class="card-body">
            <?php
            $recent_apps_query = "SELECT a.id, s.name, a.status, a.applied_at 
                                 FROM applications a
                                 JOIN scholarships s ON a.scholarship_id = s.id
                                 WHERE a.user_id = $user_id
                                 ORDER BY a.applied_at DESC
                                 LIMIT 5";
            $recent_apps_result = mysqli_query($conn, $recent_apps_query);
            
            if (mysqli_num_rows($recent_apps_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Scholarship</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($app = mysqli_fetch_assoc($recent_apps_result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $app['status'] == 'approved' ? 'success' : 
                                             ($app['status'] == 'rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo ucfirst($app['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="application_details.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <a href="my_applications.php" class="btn btn-primary mt-3">View All Applications</a>
            <?php else: ?>
                <div class="alert alert-info">
                    You haven't applied to any scholarships yet.
                    <a href="view_scholarships.php" class="alert-link">Browse available scholarships</a> to get started.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Upcoming Scholarship Deadlines -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Upcoming Scholarship Deadlines</h4>
        </div>
        <div class="card-body">
            <?php
            $upcoming_query = "SELECT s.id, s.name, s.deadline 
                              FROM scholarships s
                              WHERE s.deadline >= CURDATE()
                              AND s.id NOT IN (
                                  SELECT scholarship_id FROM applications 
                                  WHERE user_id = $user_id
                              )
                              ORDER BY s.deadline ASC
                              LIMIT 5";
            $upcoming_result = mysqli_query($conn, $upcoming_query);
            
            if (mysqli_num_rows($upcoming_result) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Scholarship</th>
                                <th>Deadline</th>
                                <th>Days Left</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($scholarship = mysqli_fetch_assoc($upcoming_result)): 
                                $days_left = date_diff(
                                    date_create(date('Y-m-d')),
                                    date_create($scholarship['deadline'])
                                )->format('%a');
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($scholarship['name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($scholarship['deadline'])); ?></td>
                                <td><?php echo $days_left; ?> days</td>
                                <td>
                                    <a href="apply_scholarship.php?id=<?php echo $scholarship['id']; ?>" class="btn btn-sm btn-success">Apply Now</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <a href="view_scholarships.php" class="btn btn-primary mt-3">View All Scholarships</a>
            <?php else: ?>
                <div class="alert alert-success">
                    You've applied to all available scholarships! Check back later for new opportunities.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="text-center mt-4 mb-5">
        <a href="Filter/global.html" class="btn btn-success btn-lg">Start Your Global Scholarships</a>
    </div>

<!-- Add this just before the footer -->
<div class="text-center mt-4 mb-5">
    <button type="button" class="btn btn-primary btn-lg ms-3" data-bs-toggle="modal" data-bs-target="#contactAdminModal">
        <i class="fas fa-envelope"></i> Contact Admin
    </button>
</div>

<!-- Contact Admin Modal -->
<div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactAdminModalLabel">Contact Administration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminContactForm">
                    <div class="mb-3">
                        <label for="contactSubject" class="form-label">Subject</label>
                        <select class="form-select" id="contactSubject" name="subject" required>
                            <option value="" selected disabled>Select a subject</option>
                            <option value="Complaint">Complaint</option>
                            <option value="Enquiry">General Enquiry</option>
                            <option value="Technical Issue">Technical Issue</option>
                            <option value="Application Help">Application Help</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contactMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="contactMessage" name="message" rows="5" required></textarea>
                    </div>
                    <div id="contactFormFeedback" class="mb-3"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitContactBtn">
                    <span id="submitContactText">Send Message</span>
                    <span id="submitContactSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add this script section before the footer -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('adminContactForm');
    const submitBtn = document.getElementById('submitContactBtn');
    const submitText = document.getElementById('submitContactText');
    const spinner = document.getElementById('submitContactSpinner');
    const feedbackEl = document.getElementById('contactFormFeedback');

    submitBtn.addEventListener('click', async function() {
        // Validate form
        if (!contactForm.checkValidity()) {
            contactForm.classList.add('was-validated');
            return;
        }

        // Prepare data
        const formData = new FormData(contactForm);
        formData.append('user_id', <?php echo $user_id; ?>);
        formData.append('username', '<?php echo $_SESSION['username']; ?>');

        // Show loading state
        submitText.textContent = 'Sending...';
        spinner.classList.remove('d-none');
        submitBtn.disabled = true;
        feedbackEl.textContent = '';
        feedbackEl.className = '';

        try {
            const response = await fetch('contact_admin.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to send message');
            }

            // Success
            feedbackEl.textContent = data.message || 'Your message has been sent successfully!';
            feedbackEl.className = 'alert alert-success';
            contactForm.reset();
            contactForm.classList.remove('was-validated');

            // Close modal after 2 seconds
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('contactAdminModal')).hide();
            }, 2000);

        } catch (error) {
            console.error('Error:', error);
            feedbackEl.textContent = error.message || 'An error occurred while sending your message';
            feedbackEl.className = 'alert alert-danger';
        } finally {
            submitText.textContent = 'Send Message';
            spinner.classList.add('d-none');
            submitBtn.disabled = false;
        }
    });
});
</script>


<?php include '../includes/footer.php'; ?>