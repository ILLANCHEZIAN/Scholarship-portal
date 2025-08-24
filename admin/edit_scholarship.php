<?php
include '../includes/config.php';


if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid scholarship ID.");
}

$id = intval($_GET['id']);



// Update scholarship
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $deadline = $_POST['deadline'];
    $caste = $_POST['caste'];
    $scheme_type = $_POST['scheme_type'];
    $gender = $_POST['gender'];

    $query = "UPDATE scholarships 
              SET name='$name', description='$description', deadline='$deadline', 
                  caste='$caste', scheme_type='$scheme_type', gender='$gender' 
              WHERE id=$id";
    mysqli_query($conn, $query);
   


    // Send email notification to users who applied for this scholarship
    $email_query = "SELECT u.email, u.username FROM users u
                    JOIN applications a ON u.id = a.user_id
                    WHERE a.scholarship_id = $id";
    $email_result = mysqli_query($conn, $email_query);

    $subject = "Scholarship Update Notification: " . htmlspecialchars($name);
    $message = "
        <html>
        <head>
          <title>Scholarship Update Notification</title>
        </head>
        <body>
          <p>Dear Applicant,</p>
          <p>The scholarship <strong>" . htmlspecialchars($name) . "</strong> has been updated. Please review the updated details and consider updating your application if necessary.</p>
          <p><a href='http://yourdomain.com/user/scholarship_details.php?id=$id'>View Scholarship Details</a></p>
          <p>Thank you,<br/>Scholarship Management Team</p>
        </body>
        </html>
    ";

    while ($row = mysqli_fetch_assoc($email_result)) {
        $to = $row['email'];
        sendEmail($to, $subject, $message);
    }

    header("Location: manage_scholarships.php");
}


// Get scholarship data
$query = "SELECT * FROM scholarships WHERE id=?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$scholarship = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$scholarship) {
    die("Scholarship not found.");
}
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Edit Scholarship</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Scholarship Name</label>
                            <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($scholarship['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5" required><?= htmlspecialchars($scholarship['description']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deadline</label>
                            <input type="date" class="form-control" name="deadline" value="<?= $scholarship['deadline'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Caste</label>
                            <select name="caste" class="form-select" required>
                                <?php
                                $castes = ['General', 'OBC', 'SC', 'ST'];
                                foreach ($castes as $c) {
                                    $selected = $scholarship['caste'] === $c ? 'selected' : '';
                                    echo "<option value='$c' $selected>$c</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Scheme Type</label>
                            <select name="scheme_type" class="form-select" required>
                                <?php
                                $types = ['Government', 'Private'];
                                foreach ($types as $type) {
                                    $selected = $scholarship['scheme_type'] === $type ? 'selected' : '';
                                    echo "<option value='$type' $selected>$type</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select" required>
                                <?php
                                $genders = ['General', 'Male', 'Female'];
                                foreach ($genders as $g) {
                                    $selected = $scholarship['gender'] === $g ? 'selected' : '';
                                    echo "<option value='$g' $selected>$g</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Update Scholarship</button>
                        <a href="manage_scholarships.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
