<?php
include 'config.php';
$job_id = $_GET['id'] ?? 0;
$message = '';
$error = '';

if ($job_id == 0) {
    header('Location: careers.php');
    exit();
}

// Handle application submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $resume = $_FILES['resume'] ?? null;

    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $resume && $resume['error'] === UPLOAD_ERR_OK) {
        // Insert application into database
        $stmt = $conn->prepare("INSERT INTO applications (job_id, applicant_name, applicant_email, applicant_phone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $job_id, $name, $email, $phone);
        if ($stmt->execute()) {
            $application_id = $stmt->insert_id;
            $stmt->close();

            // Handle file upload
            $upload_dir = 'uploads/resumes/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_name = $application_id . '-' . uniqid() . '-' . basename($resume['name']);
            $target_path = $upload_dir . $file_name;
            $file_type = $resume['type'];

            if (move_uploaded_file($resume['tmp_name'], $target_path)) {
                // Insert document record
                $doc_stmt = $conn->prepare("INSERT INTO application_documents (application_id, file_path, original_filename, file_type) VALUES (?, ?, ?, ?)");
                $doc_stmt->bind_param("isss", $application_id, $file_name, $resume['name'], $file_type);
                $doc_stmt->execute();
                $doc_stmt->close();
            }

            $message = "Application submitted successfully! Your application ID is " . $application_id . ". You can use this to track your status.";
        } else {
            $error = "Error submitting application. Please try again.";
        }
    } else {
        $error = "Please fill all required fields correctly and upload your resume.";
    }
}

// Fetch job details from database
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND status = 'Open'");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header('Location: careers.php');
    exit();
}
$job = $result->fetch_assoc();
$stmt->close();

$page_title = htmlspecialchars($job['title']) . ' - Starbucks HR';
include 'header.php';
?>
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                <p class="text-muted"><?php echo htmlspecialchars($job['location']); ?></p>
                <div class="job-meta d-flex text-muted small gap-3 mb-3">
                    <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                    <span><i class="bi bi-currency-dollar"></i> <?php echo htmlspecialchars($job['pay']); ?></span>
                    <span><i class="bi bi-clock"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                    <span><i class="bi bi-building"></i> <?php echo htmlspecialchars($job['department']); ?></span>
                </div>
                <hr>
                
                <h3>Job Description</h3>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                
                <h3>Qualifications</h3>
                <p><?php echo nl2br(htmlspecialchars($job['qualifications'])); ?></p>
            </div>
            <div class="col-md-4">
                <div class="card" id="apply-form">
                    <div class="card-body">
                        <h5 class="card-title">Apply for this position</h5>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <h6><i class="bi bi-check-circle"></i> Application Submitted!</h6>
                                <p class="mb-2">Your application has been received successfully.</p>
                                <p class="mb-2"><strong>Application ID:</strong> <?php echo $application_id; ?></p>
                                <p class="mb-3"><strong>Position:</strong> <?php echo htmlspecialchars($job['title']); ?></p>
                                <div class="d-grid gap-2">
                                    <a href="track_application.php" class="btn btn-primary btn-sm">
                                        <i class="bi bi-search"></i> Track Your Application
                                    </a>
                                    <a href="careers.php" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-left"></i> Back to Careers
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <?php if (!$message): ?>
                        <form method="post" enctype="multipart/form-data" action="job_details.php?id=<?php echo $job_id; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="resume" class="form-label">Resume (PDF, DOC, DOCX)</label>
                                <input class="form-control" type="file" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Submit Application</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <a href="careers.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> Back to Careers</a>
    </div>
<?php include 'footer.php'; ?> 