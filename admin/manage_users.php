<?php
include '../includes/config.php';
if (!isAdmin()) {
    header("Location: ../login.php?type=admin");
    exit();
}

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM users WHERE id=$id AND user_type='user'";
    mysqli_query($conn, $query);
    header("Location: manage_users.php");
}
?>

<?php include '../includes/header.php'; ?>
<h2 class="mt-4">Manage Users</h2>
<a href="dashboard.php" class="btn btn-primary mb-3">Go Back to Admin Dashboard</a>
<div class="card mt-4">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM users WHERE user_type='user'";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>
                        <a href="manage_users.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<br>
<br>
<?php include '../includes/footer.php'; ?>