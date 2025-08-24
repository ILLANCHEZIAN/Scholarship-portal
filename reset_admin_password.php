<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = ? WHERE username = 'admin' AND user_type = 'admin'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $hashed_password);
    if (mysqli_stmt_execute($stmt)) {
        echo "Admin password has been updated successfully.";
    } else {
        echo "Error updating password: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Admin Password</title>
</head>
<body>
    <h2>Reset Admin Password</h2>
    <form method="POST">
        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
