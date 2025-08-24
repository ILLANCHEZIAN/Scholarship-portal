<?php
include '../includes/config.php';
if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

// Add scholarship
// In your add scholarship form processing

$errors = [];
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $degree_level = $_POST['degree_level'];
    $amount = $_POST['amount'];
    $eligibility = $_POST['eligibility'];
    $deadline = $_POST['deadline'];
    $eligible_caste = $_POST['eligible_caste'];
    $eligible_gender = $_POST['eligible_gender'];
    $scheme_type = $_POST['scheme_type'];

    // Validate deadline is not in the past
    $today = date('Y-m-d');
    if ($deadline < $today) {
        $errors[] = "Deadline cannot be in the past.";
    }

    if (empty($errors)) {
        $query = "INSERT INTO scholarships (name, description, degree_level, amount, eligibility_criteria, deadline, eligible_caste, eligible_gender, scheme_type) 
                  VALUES ('$name', '$description', '$degree_level', '$amount', '$eligibility', '$deadline', '$eligible_caste', '$eligible_gender', '$scheme_type')";
        mysqli_query($conn, $query);
    }
}

// Delete scholarship
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM scholarships WHERE id=$id";
    mysqli_query($conn, $query);
    header("Location: manage_scholarships.php");
}
?>

<?php include '../includes/header.php'; ?>
<h2 class="mt-4">Manage Scholarships</h2>

<!-- Go Back to Admin Dashboard Button -->
<div class="mt-4">
    <a href="dashboard.php" class="btn btn-primary">Go Back to Admin Dashboard</a>
</div>

<!-- Add Scholarship Form -->
<div class="card mt-4">
    <div class="card-header">
        <h4>Add New Scholarship</h4>
    </div>
    
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Scholarship Name</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Degree Level</label>
                <select name="degree_level" class="form-select" required>
                    <option value="Undergraduate">Undergraduate</option>
                    <option value="Graduate">Graduate</option>
                    <option value="Postgraduate">Postgraduate</option>
                    <option value="PhD">PhD</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Scholarship Amount (INR)</label>
                <input type="number" step="0.01" class="form-control" name="amount" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Eligibility Criteria</label>
                <textarea class="form-control" name="eligibility" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Eligible Caste</label>
                <select name="eligible_caste" class="form-select" required>
                    <option value="General">General</option>
                    <option value="OBC">OBC</option>
                    <option value="SC">SC</option>
                    <option value="ST">ST</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Eligible Gender</label>
                <select name="eligible_gender" class="form-select" required>
                    <option value="General">General</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Scheme Type</label>
                <select name="scheme_type" class="form-select" required>
                    <option value="Government">Government</option>
                    <option value="Private">Private</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="date" class="form-control" name="deadline" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Add Scholarship</button>
        </form>
    </div>
</div>

<!-- Scholarships List -->
<div class="card mt-4">
    <div class="card-header">
        <h4>Current Scholarships</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Degree</th>
                    <th>Amount</th>
                    <th>Caste</th>
                    <th>Gender</th>
                    <th>Scheme</th>
                    <th>Deadline</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM scholarships ORDER BY deadline";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['degree_level'] ?></td>
                    <td>₹<?= number_format($row['amount'], 2) ?></td>
                    <td><?= $row['eligible_caste'] ?></td>
                    <td><?= $row['eligible_gender'] ?></td>
                    <td><?= $row['scheme_type'] ?></td>
                    <td><?= date('M d, Y', strtotime($row['deadline'])) ?></td>
                    <td>
                        <a href="edit_scholarship.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="manage_scholarships.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
