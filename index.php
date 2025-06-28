<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Starbucks HR Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar, .footer { background: #003d29; }
        .navbar-brand, .footer, .footer a, .footer p { color: #fff !important; }
        .hero { position: relative; background-image: url('star1.jpg'); background-size: cover; background-position: center; color: #fff; padding: 80px 0 60px 0; text-align: center; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .hero .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 1; }   
        .hero .container { position: relative; z-index: 2; }
        .hero-icon { font-size: 60px; margin-bottom: 20px; }
        .btn-success, .badge-custom { background: #006241; border: none; }
        .btn-success:hover { background: #004d34; }
        .stats-section { background: #f4f8f6; padding: 40px 0 20px 0; }
        .stat { font-size: 2.2rem; font-weight: bold; color: #006241; }
        .why-section { padding: 40px 0 20px 0; }
        .why-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px #0001; padding: 30px; margin-bottom: 20px; }
        .values-section { background: #d6f5e3; padding: 40px 0 20px 0; }
        .value-icon { font-size: 40px; margin-bottom: 10px; color: #006241; }
        .footer { padding: 40px 0 20px 0; }
        .footer .row > div { margin-bottom: 20px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php" style="display:flex;align-items:center;">
                <span style="display:inline-block;width:40px;height:40px;margin-right:10px;vertical-align:middle;">
                <img src="starbslogo.png" alt="Starbucks Logo" style="width:100%;height:100%;border-radius:50%;">
                </span>
                Starbucks HR
                <span class="fs-6 fw-normal" style="color:#b2dfdb;"> &nbsp&nbspManagement System</span>
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="careers.php">Careers</a></li>
                    <li class="nav-item"><a class="nav-link" href="events.php">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="policies.php">Policies</a></li>
                    <li class="nav-item"><a class="nav-link" href="articles.php">Articles</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                </ul>
                <a href="login.php" class="btn btn-outline-light ms-3">Sign In</a>
                <a href="careers.php" class="btn btn-success ms-2">Apply Now</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="overlay"></div>
        <div class="container">
            <div class="hero-icon" style="margin-bottom:20px;">
                <span style="display:inline-block;width:80px;height:80px;">
                <img src="starbslogo.png" alt="Starbucks Icon" style="width:100%;height:100%;">
                </span>
            </div>
            <h1 class="display-3 fw-bold">Your Career Journey<br>Starts Here</h1>
            <p class="lead mb-4">Join our mission to inspire and nurture the human spirit – one person,<br>one cup and one neighborhood at a time.</p>
            <div class="d-flex justify-content-center gap-3 mb-3">
                <a href="careers.php" class="btn btn-light btn-lg">Explore Careers &rarr;</a>
                <a href="employee_portal.php" class="btn btn-outline-light btn-lg">Access Employee Portal</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="stat">380,000+</div>
                    <div>Partners Worldwide</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat">33,000+</div>
                    <div>Stores Globally</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat">80+</div>
                    <div>Countries</div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat">30+</div>
                    <div>Years of Excellence</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Starbucks -->
    <section class="why-section text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">Why Choose Starbucks?</h2>
            <p class="mb-5">We're committed to creating an inclusive workplace where everyone feels valued and empowered to succeed.</p>
            <div class="row justify-content-center">
                <div class="col-md-3 why-card mx-2">
                    <div class="mb-2"><img src="https://img.icons8.com/ios-filled/40/006241/briefcase.png"/></div>
                    <h5 class="fw-bold">Career Opportunities</h5>
                    <p>Explore diverse roles across our global organization with competitive benefits and growth opportunities.</p>
                </div>
                <div class="col-md-3 why-card mx-2">
                    <div class="mb-2"><img src="https://img.icons8.com/ios-filled/40/006241/conference-call.png"/></div>
                    <h5 class="fw-bold">Employee Portal</h5>
                    <p>Access your personal dashboard, payslips, leave requests, and company updates all in one place.</p>
                </div>
                <div class="col-md-3 why-card mx-2">
                    <div class="mb-2"><img src="https://img.icons8.com/ios-filled/40/006241/graduation-cap.png"/></div>
                    <h5 class="fw-bold">Training & Development</h5>
                    <p>Continuous learning opportunities including tuition assistance and professional development programs.</p>
                </div>
                <div class="col-md-3 why-card mx-2">
                    <div class="mb-2"><img src="https://img.icons8.com/ios-filled/40/006241/like--v1.png"/></div>
                    <h5 class="fw-bold">Benefits & Wellness</h5>
                    <p>Comprehensive health coverage, mental health support, and wellness programs for you and your family.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">Our Values Drive Everything We Do</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 mb-3">
                    <div class="value-icon"><img src="https://img.icons8.com/ios-filled/40/006241/shield.png"/></div>
                    <h5 class="fw-bold">Acting with Integrity</h5>
                    <p>We conduct our business ethically and transparently, building trust through our actions.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="value-icon"><img src="https://img.icons8.com/ios-filled/40/006241/conference-call.png"/></div>
                    <h5 class="fw-bold">Being Present</h5>
                    <p>We connect authentically with our customers, communities, and each other.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="value-icon"><img src="https://img.icons8.com/ios-filled/40/006241/line-chart.png"/></div>
                    <h5 class="fw-bold">Delivering Our Best</h5>
                    <p>We pursue excellence in everything we do, from coffee quality to customer service.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ready to Start Section -->
    <section class="text-center py-5">
        <div class="container">
            <h2 class="fw-bold mb-3">Ready to Start Your Journey?</h2>
            <p class="mb-4">Whether you're looking for your first job or your next career move, we have opportunities for passionate individuals at every level.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="careers.php" class="btn btn-success btn-lg">View Open Positions</a>
                <a href="track_application.php" class="btn btn-outline-primary btn-lg">Track Application</a>
                <a href="employee_portal.php" class="btn btn-outline-dark btn-lg">Access Employee Portal</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <h5 class="fw-bold">Starbucks HR</h5>
                    <p>Join our team and be part of creating the Starbucks Experience for millions of customers worldwide.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="careers.php">Careers</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="policies.php">Policies</a></li>
                        <li><a href="articles.php">Articles</a></li>
                        <li><a href="contact.php">Contact HR</a></li>
                        <li><a href="company_updates.php"></a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>For Employees</h6>
                    <ul class="list-unstyled">
                        <li><a href="employee_portal.php">Employee Portal</a></li>
                        <li><a href="hr_documents.php">HR Documents</a></li>
                        <li><a href="help_desk.php">Help Desk</a></li>
                        <li><a href="wellness.php">Wellness & Mental Health</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h6>Contact Info</h6>
                    <ul class="list-unstyled">
                        <li><a href="mailto:hr@starbucks.com">hr@starbucks.com</a></li>
                        <li>1-800-STARBUCKS</li>
                        <li>Pureza Street</li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-3">
                &copy; 2025 Starbucks - Team 7. All rights reserved.
            </div>
        </div>
    </footer>
    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</body>
</html>
