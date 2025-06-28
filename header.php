<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title ?? 'Starbucks HR Management System'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="starbucks_theme.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                Starbucks HR Management System
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="careers.php">Careers</a></li>
                    <li class="nav-item"><a class="nav-link" href="track_application.php">Track Application</a></li>
                    <li class="nav-item"><a class="nav-link" href="articles.php">News</a></li>
                    <li class="nav-item"><a class="nav-link" href="policies.php">Policies</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <div class="d-flex ms-3">
                    <a href="login.php" class="btn btn-outline-light me-2">Sign In</a>
                    <a href="careers.php" class="btn btn-success">Apply Now</a>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-shrink-0"> 