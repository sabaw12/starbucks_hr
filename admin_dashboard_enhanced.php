<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get statistics
$stats = [];
$stats['total_employees'] = $conn->query("SELECT COUNT(*) as count FROM employees WHERE archived = 0")->fetch_assoc()['count'];
$stats['total_applications'] = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
$stats['pending_leaves'] = $conn->query("SELECT COUNT(*) as count FROM leaves WHERE status = 'Pending'")->fetch_assoc()['count'];
$stats['total_jobs'] = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE status = 'Open'")->fetch_assoc()['count'];

// Get employees
$employees_result = $conn->query("SELECT * FROM employees WHERE archived = 0 ORDER BY created_at DESC");
$employees = $employees_result->fetch_all(MYSQLI_ASSOC);

// Get applications with job details and documents
$applications_result = $conn->query("
    SELECT a.*, j.title as job_title, j.department,
           GROUP_CONCAT(CONCAT(ad.id, ':', ad.original_filename, ':', ad.file_path) SEPARATOR '|') as documents
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    LEFT JOIN application_documents ad ON a.id = ad.application_id
    GROUP BY a.id
    ORDER BY a.applied_at DESC
");
$applications = $applications_result->fetch_all(MYSQLI_ASSOC);

// Get leave requests with employee details
$leaves_result = $conn->query("
    SELECT l.*, e.name as employee_name, e.email as employee_email 
    FROM leaves l 
    JOIN employees e ON l.employee_id = e.id 
    ORDER BY l.created_at DESC
");
$leaves = $leaves_result->fetch_all(MYSQLI_ASSOC);

// Get jobs
$jobs_result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
$jobs = $jobs_result->fetch_all(MYSQLI_ASSOC);

// Get announcements
$announcements_result = $conn->query("SELECT * FROM announcements ORDER BY date DESC");
$announcements = $announcements_result->fetch_all(MYSQLI_ASSOC);

// Get payslips with employee details
$payslips_result = $conn->query("
    SELECT p.*, e.name as employee_name, e.email as employee_email 
    FROM payslips p 
    JOIN employees e ON p.employee_id = e.id 
    ORDER BY p.pay_period DESC, e.name ASC
");
$payslips = $payslips_result->fetch_all(MYSQLI_ASSOC);

// Handle success/error messages
$message = '';
if (isset($_SESSION['success'])) {
    $message = '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    $message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error'] . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Starbucks HR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #003d29; }
        .navbar-brand, .navbar-nav .nav-link { color: #fff !important; }
        .nav-tabs .nav-link.active { background-color: #006241; color: white; border-color: #006241; }
        .card { margin-bottom: 1.5rem; }
        .stats-card { background: linear-gradient(135deg, #006241, #00b894); color: white; }
        .table th { background-color: #f8f9fa; }
        .status-badge { padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-reviewed { background-color: #cce5ff; color: #004085; }
        .status-shortlisted { background-color: #e2e3ff; color: #383d41; }
        .status-interview { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-accepted { background-color: #d4edda; color: #155724; }
        .status-active { background-color: #d4edda; color: #155724; }
        .status-inactive { background-color: #f8d7da; color: #721c24; }
        .btn-group-sm .btn { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">
                <img src="starbslogo.png" alt="Starbucks Logo" style="width:40px;height:40px;border-radius:50%;margin-right:10px;">
                Starbucks HR - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        Admin Panel
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php">Public Site</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <?php if($message) echo $message; ?>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-1"></i>
                        <h3><?php echo $stats['total_employees']; ?></h3>
                        <p class="mb-0">Total Employees</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bi bi-file-earmark-text fs-1"></i>
                        <h3><?php echo $stats['total_applications']; ?></h3>
                        <p class="mb-0">Applications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bi bi-calendar-check fs-1"></i>
                        <h3><?php echo $stats['pending_leaves']; ?></h3>
                        <p class="mb-0">Pending Leaves</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body text-center">
                        <i class="bi bi-briefcase fs-1"></i>
                        <h3><?php echo $stats['total_jobs']; ?></h3>
                        <p class="mb-0">Open Jobs</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button">
                    <i class="bi bi-people"></i> Employees
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications" type="button">
                    <i class="bi bi-file-earmark-text"></i> Applications
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="leaves-tab" data-bs-toggle="tab" data-bs-target="#leaves" type="button">
                    <i class="bi bi-calendar-check"></i> Leave Requests
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="jobs-tab" data-bs-toggle="tab" data-bs-target="#jobs" type="button">
                    <i class="bi bi-briefcase"></i> Job Openings
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payroll-tab" data-bs-toggle="tab" data-bs-target="#payroll" type="button">
                    <i class="bi bi-receipt"></i> Payroll
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button">
                    <i class="bi bi-megaphone"></i> Announcements
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabContent">
            <!-- Employees Tab -->
            <div class="tab-pane fade show active" id="employees" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-people"></i> Employee Management</h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                            <i class="bi bi-plus"></i> Add Employee
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Department</th>
                                        <th>Hire Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($employees as $employee): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($employee['name']); ?></td>
                                        <td><?php echo htmlspecialchars($employee['email']); ?></td>
                                        <td><?php echo htmlspecialchars($employee['position'] ?? 'Not Set'); ?></td>
                                        <td><?php echo htmlspecialchars($employee['department'] ?? 'Not Set'); ?></td>
                                        <td><?php echo $employee['hire_date'] ? date('M d, Y', strtotime($employee['hire_date'])) : 'Not Set'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($employee['status']); ?>">
                                                <?php echo htmlspecialchars($employee['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-employee" 
                                                    data-id="<?php echo $employee['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($employee['name']); ?>"
                                                    data-email="<?php echo htmlspecialchars($employee['email']); ?>"
                                                    data-position="<?php echo htmlspecialchars($employee['position'] ?? ''); ?>"
                                                    data-department="<?php echo htmlspecialchars($employee['department'] ?? ''); ?>"
                                                    data-salary="<?php echo $employee['salary']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editEmployeeModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="post" action="admin_actions.php" class="d-inline">
                                                <input type="hidden" name="action" value="archive_employee">
                                                <input type="hidden" name="employee_id" value="<?php echo $employee['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Archive this employee?')">
                                                    <i class="bi bi-archive"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Tab -->
            <div class="tab-pane fade" id="applications" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-text"></i> Job Applications</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Job Title</th>
                                        <th>Department</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Documents</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($applications as $app): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($app['applicant_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['applicant_email']); ?></small><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['applicant_phone'] ?? ''); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                        <td><?php echo htmlspecialchars($app['department']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($app['applied_at'])); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $app['status'])); ?>">
                                                <?php echo htmlspecialchars($app['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($app['documents']): ?>
                                                <?php 
                                                $docs = explode('|', $app['documents']);
                                                foreach($docs as $doc): 
                                                    if($doc):
                                                        $doc_parts = explode(':', $doc);
                                                        if(count($doc_parts) >= 3):
                                                ?>
                                                <a href="admin_actions.php?download_doc=<?php echo $doc_parts[0]; ?>" 
                                                   class="btn btn-sm btn-outline-info mb-1" target="_blank">
                                                    <i class="bi bi-download"></i> <?php echo htmlspecialchars($doc_parts[1]); ?>
                                                </a><br>
                                                <?php 
                                                        endif;
                                                    endif;
                                                endforeach; 
                                                ?>
                                            <?php else: ?>
                                                <small class="text-muted">No documents</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical btn-group-sm" role="group">
                                                <form method="post" action="admin_actions.php" class="mb-1">
                                                    <input type="hidden" name="action" value="update_application_status">
                                                    <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                        <option value="">Change Status</option>
                                                        <option value="Pending" <?php echo $app['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Reviewed" <?php echo $app['status'] == 'Reviewed' ? 'selected' : ''; ?>>Reviewed</option>
                                                        <option value="Shortlisted" <?php echo $app['status'] == 'Shortlisted' ? 'selected' : ''; ?>>Shortlisted</option>
                                                        <option value="Interview Scheduled" <?php echo $app['status'] == 'Interview Scheduled' ? 'selected' : ''; ?>>Interview Scheduled</option>
                                                        <option value="Accepted" <?php echo $app['status'] == 'Accepted' ? 'selected' : ''; ?>>Accepted</option>
                                                        <option value="Rejected" <?php echo $app['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                    </select>
                                                </form>
                                                <?php if($app['status'] == 'Shortlisted' || $app['status'] == 'Interview Scheduled'): ?>
                                                <button class="btn btn-sm btn-outline-warning schedule-interview" 
                                                        data-id="<?php echo $app['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($app['applicant_name']); ?>"
                                                        data-email="<?php echo htmlspecialchars($app['applicant_email']); ?>"
                                                        data-job="<?php echo htmlspecialchars($app['job_title']); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#scheduleInterviewModal">
                                                    <i class="bi bi-calendar-plus"></i> Schedule
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Requests Tab -->
            <div class="tab-pane fade" id="leaves" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-calendar-check"></i> Leave Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Dates</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($leaves as $leave): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($leave['employee_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($leave['employee_email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($leave['leave_type']); ?></td>
                                        <td>
                                            <?php echo date('M d', strtotime($leave['start_date'])); ?> - 
                                            <?php echo date('M d, Y', strtotime($leave['end_date'])); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars(substr($leave['reason'], 0, 50)) . '...'; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($leave['status']); ?>">
                                                <?php echo htmlspecialchars($leave['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if($leave['status'] === 'Pending'): ?>
                                            <button class="btn btn-sm btn-outline-primary process-leave" 
                                                    data-id="<?php echo $leave['id']; ?>"
                                                    data-employee="<?php echo htmlspecialchars($leave['employee_name']); ?>"
                                                    data-type="<?php echo htmlspecialchars($leave['leave_type']); ?>"
                                                    data-dates="<?php echo date('M d', strtotime($leave['start_date'])) . ' - ' . date('M d, Y', strtotime($leave['end_date'])); ?>"
                                                    data-reason="<?php echo htmlspecialchars($leave['reason']); ?>"
                                                    data-bs-toggle="modal" data-bs-target="#processLeaveModal">
                                                Process
                                            </button>
                                            <?php else: ?>
                                            <small class="text-muted">Processed</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jobs Tab -->
            <div class="tab-pane fade" id="jobs" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-briefcase"></i> Job Openings</h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addJobModal">
                            <i class="bi bi-plus"></i> Add Job
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Department</th>
                                        <th>Location</th>
                                        <th>Type</th>
                                        <th>Pay</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($jobs as $job): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($job['title']); ?></td>
                                        <td><?php echo htmlspecialchars($job['department']); ?></td>
                                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                                        <td><?php echo htmlspecialchars($job['job_type']); ?></td>
                                        <td><?php echo htmlspecialchars($job['pay']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($job['status']); ?>">
                                                <?php echo htmlspecialchars($job['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-job" 
                                                    data-id="<?php echo $job['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($job['title']); ?>"
                                                    data-department="<?php echo htmlspecialchars($job['department']); ?>"
                                                    data-location="<?php echo htmlspecialchars($job['location']); ?>"
                                                    data-job-type="<?php echo htmlspecialchars($job['job_type']); ?>"
                                                    data-pay="<?php echo htmlspecialchars($job['pay']); ?>"
                                                    data-description="<?php echo htmlspecialchars($job['description']); ?>"
                                                    data-qualifications="<?php echo htmlspecialchars($job['qualifications']); ?>"
                                                    data-status="<?php echo htmlspecialchars($job['status']); ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editJobModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="post" action="admin_actions.php" class="d-inline">
                                                <input type="hidden" name="action" value="delete_job">
                                                <input type="hidden" name="job_id" value="<?php echo $job['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this job?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Tab -->
            <div class="tab-pane fade" id="payroll" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-receipt"></i> Payroll Management</h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generatePayslipModal">
                            <i class="bi bi-plus"></i> Generate Payslip
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Pay Period</th>
                                        <th>Basic Salary</th>
                                        <th>Allowances</th>
                                        <th>Deductions</th>
                                        <th>Net Pay</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payslips as $payslip): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($payslip['employee_name']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($payslip['employee_email']); ?></small>
                                        </td>
                                        <td><?php echo date('M Y', strtotime($payslip['pay_period'])); ?></td>
                                        <td>₱<?php echo number_format($payslip['basic_salary'], 2); ?></td>
                                        <td>₱<?php echo number_format($payslip['allowances'], 2); ?></td>
                                        <td>₱<?php echo number_format($payslip['deductions'] + $payslip['tax'], 2); ?></td>
                                        <td><strong>₱<?php echo number_format($payslip['net_pay'], 2); ?></strong></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-payslip" 
                                                    data-id="<?php echo $payslip['id']; ?>"
                                                    data-employee-id="<?php echo $payslip['employee_id']; ?>"
                                                    data-employee-name="<?php echo htmlspecialchars($payslip['employee_name']); ?>"
                                                    data-period="<?php echo $payslip['pay_period']; ?>"
                                                    data-basic="<?php echo $payslip['basic_salary']; ?>"
                                                    data-allowances="<?php echo $payslip['allowances']; ?>"
                                                    data-deductions="<?php echo $payslip['deductions']; ?>"
                                                    data-tax="<?php echo $payslip['tax']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editPayslipModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Announcements Tab -->
            <div class="tab-pane fade" id="announcements" role="tabpanel">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-megaphone"></i> Company Announcements</h5>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                            <i class="bi bi-plus"></i> Add Announcement
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Content</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($announcements as $announcement): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($announcement['title']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($announcement['content'] ?? '', 0, 100)) . '...'; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($announcement['date'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary edit-announcement" 
                                                    data-id="<?php echo $announcement['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($announcement['title']); ?>"
                                                    data-content="<?php echo htmlspecialchars($announcement['content'] ?? ''); ?>"
                                                    data-date="<?php echo $announcement['date']; ?>"
                                                    data-bs-toggle="modal" data-bs-target="#editAnnouncementModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="post" action="admin_actions.php" class="d-inline">
                                                <input type="hidden" name="action" value="delete_announcement">
                                                <input type="hidden" name="announcement_id" value="<?php echo $announcement['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this announcement?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="add_employee">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department" required>
                                <option value="">Select Department</option>
                                <option value="Store Operations">Store Operations</option>
                                <option value="Management">Management</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Finance">Finance</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hire Date</label>
                            <input type="date" class="form-control" name="hire_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Salary</label>
                            <input type="number" class="form-control" name="salary" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="update_employee">
                    <input type="hidden" name="employee_id" id="edit_employee_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Position</label>
                            <input type="text" class="form-control" name="position" id="edit_position" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select class="form-select" name="department" id="edit_department" required>
                                <option value="">Select Department</option>
                                <option value="Store Operations">Store Operations</option>
                                <option value="Management">Management</option>
                                <option value="Human Resources">Human Resources</option>
                                <option value="Finance">Finance</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Salary</label>
                            <input type="number" class="form-control" name="salary" id="edit_salary" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Employee</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Schedule Interview Modal -->
    <div class="modal fade" id="scheduleInterviewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="schedule_interview">
                    <input type="hidden" name="application_id" id="interview_app_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Schedule Interview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Applicant:</strong> <span id="interview_name"></span><br>
                            <strong>Email:</strong> <span id="interview_email"></span><br>
                            <strong>Position:</strong> <span id="interview_job"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Interview Date</label>
                            <input type="date" class="form-control" name="interview_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Interview Time</label>
                            <input type="time" class="form-control" name="interview_time" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Interview Location/Platform</label>
                            <input type="text" class="form-control" name="interview_location" placeholder="e.g., Conference Room A or Zoom Meeting" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Interviewer</label>
                            <input type="text" class="form-control" name="interviewer" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="interview_notes" rows="3" placeholder="Additional instructions for the candidate"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Schedule Interview</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Process Leave Modal -->
    <div class="modal fade" id="processLeaveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="process_leave">
                    <input type="hidden" name="leave_id" id="process_leave_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Process Leave Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Employee:</strong> <span id="process_employee"></span><br>
                            <strong>Leave Type:</strong> <span id="process_type"></span><br>
                            <strong>Dates:</strong> <span id="process_dates"></span><br>
                            <strong>Reason:</strong> <span id="process_reason"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Decision</label>
                            <select class="form-select" name="leave_status" required>
                                <option value="">Select Decision</option>
                                <option value="Approved">Approve</option>
                                <option value="Rejected">Reject</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin Notes</label>
                            <textarea class="form-control" name="admin_notes" rows="3" placeholder="Optional notes for the employee"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Process Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Job Modal -->
    <div class="modal fade" id="addJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="add_job">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Job Opening</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-select" name="department" required>
                                    <option value="">Select Department</option>
                                    <option value="Store Operations">Store Operations</option>
                                    <option value="Management">Management</option>
                                    <option value="Human Resources">Human Resources</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Type</label>
                                <select class="form-select" name="job_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Contract">Contract</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pay Range</label>
                            <input type="text" class="form-control" name="pay" placeholder="e.g., $15-18/hour" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Job Description</label>
                            <textarea class="form-control" name="description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qualifications</label>
                            <textarea class="form-control" name="qualifications" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Job Modal -->
    <div class="modal fade" id="editJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="update_job">
                    <input type="hidden" name="job_id" id="edit_job_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Job Opening</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Title</label>
                                <input type="text" class="form-control" name="title" id="edit_job_title" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select class="form-select" name="department" id="edit_job_department" required>
                                    <option value="">Select Department</option>
                                    <option value="Store Operations">Store Operations</option>
                                    <option value="Management">Management</option>
                                    <option value="Human Resources">Human Resources</option>
                                    <option value="Finance">Finance</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" id="edit_job_location" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Type</label>
                                <select class="form-select" name="job_type" id="edit_job_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Full-time">Full-time</option>
                                    <option value="Part-time">Part-time</option>
                                    <option value="Contract">Contract</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pay Range</label>
                                <input type="text" class="form-control" name="pay" id="edit_job_pay" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" id="edit_job_status" required>
                                    <option value="Open">Open</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Job Description</label>
                            <textarea class="form-control" name="description" id="edit_job_description" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qualifications</label>
                            <textarea class="form-control" name="qualifications" id="edit_job_qualifications" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Generate Payslip Modal -->
    <div class="modal fade" id="generatePayslipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="generate_payslip">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Payslip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select class="form-select" name="employee_id" required>
                                <option value="">Select Employee</option>
                                <?php foreach($employees as $emp): ?>
                                <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pay Period</label>
                            <input type="month" class="form-control" name="pay_period" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Basic Salary</label>
                            <input type="number" class="form-control" name="basic_salary" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allowances</label>
                            <input type="number" class="form-control" name="allowances" step="0.01" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deductions</label>
                            <input type="number" class="form-control" name="deductions" step="0.01" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax</label>
                            <input type="number" class="form-control" name="tax" step="0.01" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Generate Payslip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Payslip Modal -->
    <div class="modal fade" id="editPayslipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="update_payslip">
                    <input type="hidden" name="payslip_id" id="edit_payslip_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Payslip</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <input type="text" class="form-control" id="edit_payslip_employee" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pay Period</label>
                            <input type="month" class="form-control" name="pay_period" id="edit_payslip_period" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Basic Salary</label>
                            <input type="number" class="form-control" name="basic_salary" id="edit_payslip_basic" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allowances</label>
                            <input type="number" class="form-control" name="allowances" id="edit_payslip_allowances" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deductions</label>
                            <input type="number" class="form-control" name="deductions" id="edit_payslip_deductions" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tax</label>
                            <input type="number" class="form-control" name="tax" id="edit_payslip_tax" step="0.01">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Payslip</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Announcement Modal -->
    <div class="modal fade" id="addAnnouncementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="add_announcement">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Announcement Modal -->
    <div class="modal fade" id="editAnnouncementModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="admin_actions.php">
                    <input type="hidden" name="action" value="update_announcement">
                    <input type="hidden" name="announcement_id" id="edit_announcement_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" id="edit_announcement_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea class="form-control" name="content" id="edit_announcement_content" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" id="edit_announcement_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Edit employee modal
        document.querySelectorAll('.edit-employee').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_employee_id').value = this.dataset.id;
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_email').value = this.dataset.email;
                document.getElementById('edit_position').value = this.dataset.position;
                document.getElementById('edit_department').value = this.dataset.department;
                document.getElementById('edit_salary').value = this.dataset.salary;
            });
        });

        // Schedule interview modal
        document.querySelectorAll('.schedule-interview').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('interview_app_id').value = this.dataset.id;
                document.getElementById('interview_name').textContent = this.dataset.name;
                document.getElementById('interview_email').textContent = this.dataset.email;
                document.getElementById('interview_job').textContent = this.dataset.job;
            });
        });

        // Process leave modal
        document.querySelectorAll('.process-leave').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('process_leave_id').value = this.dataset.id;
                document.getElementById('process_employee').textContent = this.dataset.employee;
                document.getElementById('process_type').textContent = this.dataset.type;
                document.getElementById('process_dates').textContent = this.dataset.dates;
                document.getElementById('process_reason').textContent = this.dataset.reason;
            });
        });

        // Edit job modal
        document.querySelectorAll('.edit-job').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_job_id').value = this.dataset.id;
                document.getElementById('edit_job_title').value = this.dataset.title;
                document.getElementById('edit_job_department').value = this.dataset.department;
                document.getElementById('edit_job_location').value = this.dataset.location;
                document.getElementById('edit_job_type').value = this.dataset.jobType;
                document.getElementById('edit_job_pay').value = this.dataset.pay;
                document.getElementById('edit_job_description').value = this.dataset.description;
                document.getElementById('edit_job_qualifications').value = this.dataset.qualifications;
                document.getElementById('edit_job_status').value = this.dataset.status;
            });
        });

        // Edit payslip modal
        document.querySelectorAll('.edit-payslip').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_payslip_id').value = this.dataset.id;
                document.getElementById('edit_payslip_employee').value = this.dataset.employeeName;
                document.getElementById('edit_payslip_period').value = this.dataset.period;
                document.getElementById('edit_payslip_basic').value = this.dataset.basic;
                document.getElementById('edit_payslip_allowances').value = this.dataset.allowances;
                document.getElementById('edit_payslip_deductions').value = this.dataset.deductions;
                document.getElementById('edit_payslip_tax').value = this.dataset.tax;
            });
        });

        // Edit announcement modal
        document.querySelectorAll('.edit-announcement').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_announcement_id').value = this.dataset.id;
                document.getElementById('edit_announcement_title').value = this.dataset.title;
                document.getElementById('edit_announcement_content').value = this.dataset.content;
                document.getElementById('edit_announcement_date').value = this.dataset.date;
            });
        });
    </script>
</body>
</html>