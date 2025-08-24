<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Scholarship Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php' ?>">Dashboard</a></li>
                <?php endif; ?>
            </ul>
            
            <?php if(isset($_SESSION['user_id'])): ?>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle fa-lg me-2"></i>
                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="dropdownUser">
                <li><a class="dropdown-item" href="user/profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                 <li><a class="dropdown-item" href="user/settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>      
                 <li><a class="dropdown-item" href="user/my_applications.php"><i class="fas fa-file-alt me-2"></i>My Applications</a></li>
                 <li><hr class="dropdown-divider"></li>
                 <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
</ul>
                </ul>
            </div>
            <?php else: ?>
                <div class="d-flex">
                    <a href="login.php?type=admin" class="btn btn-outline-light me-2">Admin</a>
                    <a href="login.php?type=user" class="btn btn-primary">User Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">