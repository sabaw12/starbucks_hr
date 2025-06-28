<?php
include 'config.php';
$application_details = null;
$interview_schedule = null;
$search_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_term = $_POST['search_term'] ?? '';
    if (!empty($search_term)) {
        // Check if it's a numeric ID or an email
        if (is_numeric($search_term)) {
            $stmt = $conn->prepare("SELECT a.*, j.title as job_title, j.department FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.id = ?");
            $stmt->bind_param("i", $search_term);
        } else {
            $stmt = $conn->prepare("SELECT a.*, j.title as job_title, j.department FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.applicant_email = ? ORDER BY a.applied_at DESC LIMIT 1");
            $stmt->bind_param("s", $search_term);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $application_details = $result->fetch_assoc();
            
            // Get interview schedule if exists
            $stmt_interview = $conn->prepare("SELECT * FROM interview_schedules WHERE application_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt_interview->bind_param("i", $application_details['id']);
            $stmt_interview->execute();
            $interview_result = $stmt_interview->get_result();
            if ($interview_result->num_rows > 0) {
                $interview_schedule = $interview_result->fetch_assoc();
            }
            $stmt_interview->close();
        } else {
            $search_error = "No application found with that ID or email address.";
        }
        $stmt->close();
    } else {
        $search_error = "Please enter an application ID or email address.";
    }
}
$page_title = 'Track Application - Starbucks HR';
include 'header.php';
?>

<style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-reviewed { background-color: #cce5ff; color: #004085; }
    .status-shortlisted { background-color: #e2e3ff; color: #383d41; }
    .status-interview { background-color: #fff3cd; color: #856404; }
    .status-accepted { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
    .interview-card {
        background: #003300;
        color: white;
        border-radius: 10px;
    }
    h4 {
        color: #ffffff;
    }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #006241;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -19px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #006241;
    }
    .timeline-item.active::before {
        background: #00b894;
        box-shadow: 0 0 0 3px rgba(0, 184, 148, 0.3);
    }
</style>

<div class="container my-5">
    <h2 class="text-center mb-4">Track Your Application</h2>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted">Enter your application ID or email address to check the status of your most recent application.</p>
                    <form method="post" action="track_application.php">
                        <div class="mb-3">
                            <label for="search_term" class="form-label">Application ID or Email</label>
                            <input type="text" class="form-control" id="search_term" name="search_term" 
                                   value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Track Status
                        </button>
                    </form>
                    
                    <?php if ($search_error): ?>
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo $search_error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($application_details): ?>
                        <div class="mt-4">
                            <!-- Application Overview -->
                            <div class="card border-0 bg-light mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h3 class="mb-3">Application Details</h3>
                                            <p class="mb-2"><strong>Application ID:</strong> #<?php echo $application_details['id']; ?></p>
                                            <p class="mb-2"><strong>Applicant:</strong> <?php echo htmlspecialchars($application_details['applicant_name']); ?></p>
                                            <p class="mb-2"><strong>Position:</strong> <?php echo htmlspecialchars($application_details['job_title']); ?></p>
                                            <p class="mb-2"><strong>Department:</strong> <?php echo htmlspecialchars($application_details['department']); ?></p>
                                            <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($application_details['applicant_email']); ?></p>
                                            <p class="mb-0"><strong>Applied on:</strong> <?php echo date('F d, Y \a\t g:i A', strtotime($application_details['applied_at'])); ?></p>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <h5 class="mb-3">Current Status</h5>
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $application_details['status'])); ?>">
                                                <?php echo htmlspecialchars($application_details['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Interview Schedule (if exists) -->
                            <?php if ($interview_schedule): ?>
                                <div class="card interview-card mb-4">
                                    <div class="card-body">
                                        <h4 class="mb-3">
                                            <i class="bi bi-calendar-check"></i> Interview Scheduled
                                        </h4>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2">
                                                    <i class="bi bi-calendar3"></i> 
                                                    <strong>Date:</strong> <?php echo date('F d, Y', strtotime($interview_schedule['interview_date'])); ?>
                                                </p>
                                                <p class="mb-2">
                                                    <i class="bi bi-clock"></i> 
                                                    <strong>Time:</strong> <?php echo date('g:i A', strtotime($interview_schedule['interview_time'])); ?>
                                                </p>
                                                <p class="mb-2">
                                                    <i class="bi bi-geo-alt"></i> 
                                                    <strong>Location:</strong> <?php echo htmlspecialchars($interview_schedule['location']); ?>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2">
                                                    <i class="bi bi-person"></i> 
                                                    <strong>Interviewer:</strong> <?php echo htmlspecialchars($interview_schedule['interviewer']); ?>
                                                </p>
                                                <p class="mb-2">
                                                    <i class="bi bi-info-circle"></i> 
                                                    <strong>Status:</strong> <?php echo htmlspecialchars($interview_schedule['status']); ?>
                                                </p>
                                                <?php if ($interview_schedule['notes']): ?>
                                                <p class="mb-0">
                                                    <i class="bi bi-sticky"></i> 
                                                    <strong>Notes:</strong> <?php echo htmlspecialchars($interview_schedule['notes']); ?>
                                                </p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php 
                                        $interview_datetime = $interview_schedule['interview_date'] . ' ' . $interview_schedule['interview_time'];
                                        $is_upcoming = strtotime($interview_datetime) > time();
                                        ?>
                                        
                                        <?php if ($is_upcoming): ?>
                                            <div class="alert alert-light mt-3 mb-0">
                                                <i class="bi bi-lightbulb"></i> 
                                                <strong>Reminder:</strong> Please arrive 10-15 minutes early for your interview.    
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Application Timeline -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="mb-4">Application Progress</h5>
                                    <div class="timeline">
                                        <div class="timeline-item active">
                                            <h6>Application Submitted</h6>
                                            <p class="text-muted mb-0"><?php echo date('M d, Y \a\t g:i A', strtotime($application_details['applied_at'])); ?></p>
                                        </div>
                                        
                                        <?php if (in_array($application_details['status'], ['Reviewed', 'Shortlisted', 'Interview Scheduled', 'Accepted', 'Rejected'])): ?>
                                        <div class="timeline-item active">
                                            <h6>Application Reviewed</h6>
                                            <p class="text-muted mb-0">Your application has been reviewed by our HR team</p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (in_array($application_details['status'], ['Shortlisted', 'Interview Scheduled', 'Accepted'])): ?>
                                        <div class="timeline-item active">
                                            <h6>Shortlisted</h6>
                                            <p class="text-muted mb-0">Congratulations! You've been shortlisted for this position</p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (in_array($application_details['status'], ['Interview Scheduled', 'Accepted'])): ?>
                                        <div class="timeline-item active">
                                            <h6>Interview Scheduled</h6>
                                            <p class="text-muted mb-0">
                                                <?php if ($interview_schedule): ?>
                                                    Interview scheduled for <?php echo date('M d, Y \a\t g:i A', strtotime($interview_schedule['interview_date'] . ' ' . $interview_schedule['interview_time'])); ?>
                                                <?php else: ?>
                                                    Interview has been scheduled
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($application_details['status'] === 'Accepted'): ?>
                                        <div class="timeline-item active">
                                            <h6>Application Accepted</h6>
                                            <p class="text-muted mb-0">Congratulations! Your application has been accepted. You will be contacted soon with next steps.</p>
                                        </div>
                                        <?php elseif ($application_details['status'] === 'Rejected'): ?>
                                        <div class="timeline-item">
                                            <h6>Application Status</h6>
                                            <p class="text-muted mb-0">Thank you for your interest. Unfortunately, we have decided to move forward with other candidates at this time.</p>
                                        </div>
                                        <?php else: ?>
                                        <div class="timeline-item">
                                            <h6>Final Decision</h6>
                                            <p class="text-muted mb-0">We will notify you of our final decision soon</p>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="careers.php" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Back to Careers
                </a>
            </div>
        </div>
    </div>
</div>