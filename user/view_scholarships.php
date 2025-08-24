<?php
include '../includes/config.php';
if (!isUser()) {
    header("Location: ../login.php?type=user");
    exit();
}
?>

<?php include '../includes/header.php'; ?>
<h2 class="mt-4">Available Scholarships</h2>

<!-- Filter Form -->
<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3">
        <input type="text" class="form-control" name="keyword" placeholder="Search by keyword" value="<?= $_GET['keyword'] ?? '' ?>">
    </div>
    <div class="col-md-2">
        <select name="degree_level" class="form-select">
            <option value="">All Degrees</option>
            <option value="Undergraduate" <?= (($_GET['degree_level'] ?? '') == 'Undergraduate') ? 'selected' : '' ?>>Undergraduate</option>
            <option value="Graduate" <?= (($_GET['degree_level'] ?? '') == 'Graduate') ? 'selected' : '' ?>>Graduate</option>
            <option value="Postgraduate" <?= (($_GET['degree_level'] ?? '') == 'Postgraduate') ? 'selected' : '' ?>>Postgraduate</option>
            <option value="PhD" <?= (($_GET['degree_level'] ?? '') == 'PhD') ? 'selected' : '' ?>>PhD</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="eligible_caste" class="form-select">
            <option value="">All Castes</option>
            <option value="General" <?= (($_GET['eligible_caste'] ?? '') == 'General') ? 'selected' : '' ?>>General</option>
            <option value="OBC" <?= (($_GET['eligible_caste'] ?? '') == 'OBC') ? 'selected' : '' ?>>OBC</option>
            <option value="SC" <?= (($_GET['eligible_caste'] ?? '') == 'SC') ? 'selected' : '' ?>>SC</option>
            <option value="ST" <?= (($_GET['eligible_caste'] ?? '') == 'ST') ? 'selected' : '' ?>>ST</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="eligible_gender" class="form-select">
            <option value="">All Genders</option>
            <option value="General" <?= (($_GET['eligible_gender'] ?? '') == 'General') ? 'selected' : '' ?>>General</option>
            <option value="Male" <?= (($_GET['eligible_gender'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (($_GET['eligible_gender'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
        </select>
    </div>
    <div class="col-md-2">
        <select name="scheme_type" class="form-select">
            <option value="">All Schemes</option>
            <option value="Government" <?= (($_GET['scheme_type'] ?? '') == 'Government') ? 'selected' : '' ?>>Government</option>
            <option value="Private" <?= (($_GET['scheme_type'] ?? '') == 'Private') ? 'selected' : '' ?>>Private</option>
        </select>
    </div>
    <div class="col-md-1 d-grid">
        <button type="submit" class="btn btn-success">Search</button>
    </div>
</form>

<h2 class="mt-4">Available Scholarships</h2>
<div class="row mt-4">
<?php
$where = ["deadline >= CURDATE()"];

if (!empty($_GET['keyword'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $where[] = "(name LIKE '%$keyword%' OR description LIKE '%$keyword%')";
}
if (!empty($_GET['degree_level'])) {
    $degree = mysqli_real_escape_string($conn, $_GET['degree_level']);
    $where[] = "degree_level = '$degree'";
}
if (!empty($_GET['eligible_caste'])) {
    $caste = mysqli_real_escape_string($conn, $_GET['eligible_caste']);
    $where[] = "eligible_caste = '$caste'";
}
if (!empty($_GET['eligible_gender'])) {
    $gender = mysqli_real_escape_string($conn, $_GET['eligible_gender']);
    $where[] = "eligible_gender = '$gender'";
}
if (!empty($_GET['scheme_type'])) {
    $scheme = mysqli_real_escape_string($conn, $_GET['scheme_type']);
    $where[] = "scheme_type = '$scheme'";
}

$condition = implode(" AND ", $where);
$query = "SELECT * FROM scholarships WHERE $condition ORDER BY deadline";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0): ?>
    <p class="text-center mt-4">No scholarships available matching your criteria.</p>
<?php else: ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?= $row['name'] ?></h5>
                <p class="card-text"><?= substr($row['description'], 0, 100) ?>...</p>
                <p class="text-muted">Deadline: <?= date('M d, Y', strtotime($row['deadline'])) ?></p>
                <a href="apply_scholarship.php?id=<?= $row['id'] ?>" class="btn btn-primary">Apply Now</a>
                <a href="scholarship_details.php?id=<?= $row['id'] ?>" class="btn btn-info ms-2">View Details</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>