<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wellness & Mental Health - Starbucks HR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar, .footer { background: #003d29; }
        .navbar-brand, .footer, .footer a, .footer p { color: #fff !important; }
        .footer { padding: 40px 0 20px 0; }
        .footer .row > div { margin-bottom: 20px; }
        .circle-icon {
            background: #b2dfdb;
            color: #003d29;
            width: 50px; height: 50px;
            border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; margin-right: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include 'header.php'; ?>

    <div class="container my-5">
        <h2 class="mb-4"><span class="circle-icon"><i class="bi bi-heart-pulse"></i></span>Wellness & Mental Health</h2>
        
        <div class="alert alert-success">
            <strong>Your mental health matters.</strong> Starbucks HR is committed to supporting your overall wellness.
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h4 class="mb-3"><i class="bi bi-emoji-smile"></i> Tips for Well-being</h4>
                <ul>
                    <li>Take regular breaks and practice deep breathing.</li>
                    <li>Stay connected with friends, family, and colleagues.</li>
                    <li>Reach out when you're feeling overwhelmed.</li>
                    <li>Pursue hobbies that bring you joy.</li>
                    <li>Maintain a healthy sleep routine.</li>
                </ul>
            </div>
            <div class="col-md-6">
                <h4 class="mb-3"><i class="bi bi-telephone-forward"></i> 24/7 Support Hotlines</h4>
                <ul>
                    <li><strong>National Mental Health Crisis Hotline (PH):</strong> 1553 (Luzon-wide landline)</li>
                    <li><strong>DOH-NCMH Crisis Hotline:</strong> 0917-899-8727 (USAP) | (02) 7989-8727</li>
                    <li><strong>Hopeline PH:</strong> (02) 804-HOPE (4673) | 0917-558-HOPE (4673)</li>
                </ul>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-chat-left-heart"></i> Employee Assistance Program (EAP)</h5>
                <p class="card-text">
                    Starbucks offers an Employee Assistance Program for confidential counseling and support. To request help, contact HR or email <a href="mailto:hr-support@starbucks.com">hr-support@starbucks.com</a>.
                </p>
            </div>
        </div>

        <div class="mb-4">
            <h4><i class="bi bi-globe"></i> Online Wellness Resources</h4>
            <ul>
                <li><a href="https://www.who.int/campaigns/world-mental-health-day" target="_blank">WHO: World Mental Health Day</a></li>
                <li><a href="https://www.pmha.org.ph/" target="_blank">Philippine Mental Health Association</a></li>
                <li><a href="https://www.ncmh.gov.ph/" target="_blank">National Center for Mental Health (NCMH)</a></li>
            </ul>
        </div>

        <a href="index.php" class="btn btn-outline-dark"><i class="bi bi-arrow-left"></i> Back to Home</a>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>