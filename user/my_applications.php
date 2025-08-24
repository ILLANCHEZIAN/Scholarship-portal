<?php
include '../includes/config.php';
if (!isUser()) {
    header("Location: ../login.php?type=user");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT a.id, s.name, a.status, a.applied_at 
          FROM applications a
          JOIN scholarships s ON a.scholarship_id = s.id
          WHERE a.user_id = $user_id
          ORDER BY a.applied_at DESC";
$result = mysqli_query($conn, $query);
?>

<?php include '../includes/header.php'; ?>
<div class="container mt-5">
    <h2>My Scholarship Applications</h2>
    <div class="card mt-4">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Scholarship</th>
                        <th>Applied On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= date('M d, Y', strtotime($row['applied_at'])) ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $row['status'] == 'approved' ? 'success' : 
                                ($row['status'] == 'rejected' ? 'danger' : 'warning') 
                            ?>">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<?php include '../includes/footer.php'; ?>