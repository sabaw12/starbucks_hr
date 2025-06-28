<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['user'];

// Get employee data
$stmt = $conn->prepare("SELECT * FROM employees WHERE email = ?");
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

// Get employee leave balance and requests
$leave_balance = 15; // Default leave balance
$leave_requests = [];
if ($employee) {
    $stmt = $conn->prepare("SELECT * FROM leaves WHERE employee_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $employee['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $leave_requests = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Get announcements
$stmt = $conn->prepare("SELECT * FROM announcements ORDER BY date DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
$announcements = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get payslips
$payslips = [];
if ($employee) {
    $stmt = $conn->prepare("SELECT * FROM payslips WHERE employee_id = ? ORDER BY pay_period DESC");
    $stmt->bind_param("i", $employee['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $payslips = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Portal - Starbucks HR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #003d29; }
        .navbar-brand, .navbar-nav .nav-link { color: #fff !important; }
        .nav-tabs .nav-link.active { background-color: #006241; color: white; border-color: #006241; }
        .card { margin-bottom: 1.5rem; }
        .leave-balance { background: linear-gradient(135deg, #006241, #00b894); color: white; }
        .announcement-card { border-left: 4px solid #006241; }
        .status-badge { padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <img src="starbslogo.png" alt="Starbucks Logo" style="width:40px;height:40px;border-radius:50%;margin-right:10px;">
                Starbucks HR
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" type="button">
                    <i class="bi bi-house"></i> Dashboard
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="leave-tab" data-bs-toggle="tab" data-bs-target="#leave" type="button">
                    <i class="bi bi-calendar-check"></i> Leave Request
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">
                    <i class="bi bi-person"></i> Personal Info
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payslips-tab" data-bs-toggle="tab" data-bs-target="#payslips" type="button">
                    <i class="bi bi-receipt"></i> Payslips
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="employeeTabContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="mb-4">Welcome, <?php echo htmlspecialchars($employee['name'] ?? $user['email']); ?>!</h3>
                        
                        <!-- Leave Balance Card -->
                        <div class="card leave-balance">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5><i class="bi bi-calendar-check"></i> Leave Balance</h5>
                                        <h2><?php echo $leave_balance; ?> days</h2>
                                        <p class="mb-0">Remaining this year</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Leave Breakdown</h6>
                                        <div class="small">
                                            <div>Vacation: 10 days</div>
                                            <div>Sick: 5 days</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Leave Requests -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-clock-history"></i> Recent Leave Requests</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($leave_requests)): ?>
                                    <p class="text-muted">No leave requests yet.</p>
                                <?php else: ?>
                                    <?php foreach(array_slice($leave_requests, 0, 3) as $leave): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <strong><?php echo htmlspecialchars($leave['leave_type']); ?></strong>
                                            <small class="text-muted d-block">
                                                <?php echo date('M d', strtotime($leave['start_date'])); ?> - 
                                                <?php echo date('M d, Y', strtotime($leave['end_date'])); ?>
                                            </small>
                                        </div>
                                        <span class="status-badge status-<?php echo strtolower($leave['status']); ?>">
                                            <?php echo htmlspecialchars($leave['status']); ?>
                                        </span>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <!-- Schedule Card -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-calendar3"></i> This Week's Schedule</h5>
                            </div>
                            <div class="card-body">
                                <div class="small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Monday</span>
                                        <span>9:00 AM - 5:00 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Tuesday</span>
                                        <span>9:00 AM - 5:00 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Wednesday</span>
                                        <span>9:00 AM - 5:00 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Thursday</span>
                                        <span>9:00 AM - 5:00 PM</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Friday</span>
                                        <span>9:00 AM - 5:00 PM</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Announcements -->
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-megaphone"></i> Announcements</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach($announcements as $announcement): ?>
                                <div class="announcement-card card mb-2">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                        <small class="text-muted"><?php echo date('M d, Y', strtotime($announcement['date'])); ?></small>
                                        <p class="card-text small mt-1"><?php echo htmlspecialchars(substr($announcement['content'], 0, 100)); ?>...</p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Request Tab -->
            <div class="tab-pane fade" id="leave" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-plus-circle"></i> Request Leave</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="employee_actions.php" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="request_leave">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Leave Type</label>
                                        <select class="form-select" name="leave_type" required>
                                            <option value="">Select type</option>
                                            <option value="Vacation">Vacation</option>
                                            <option value="Sick">Sick Leave</option>
                                            <option value="Emergency">Emergency</option>
                                            <option value="Personal">Personal</option>
                                            <option value="Maternity">Maternity Leave</option>
                                            <option value="Paternity">Paternity Leave</option>
                                            <option value="Bereavement">Bereavement Leave</option>
                                            <option value="Unpaid">Unpaid Leave</option>
                                            <option value="Sabbatical">Sabbatical Leave</option>
                                            <option value="Medical">Medical Leave</option>
                                            <option value="Election">Election Leave</option>
                                        </select>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Start Date</label>
                                            <input type="date" class="form-control" name="start_date" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">End Date</label>
                                            <input type="date" class="form-control" name="end_date" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Reason</label>
                                        <textarea class="form-control" name="reason" rows="3" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Supporting Document</label>
                                        <input type="file" class="form-control" name="document" accept=".pdf,.doc,.docx,.jpg,.png" required>
                                        <small class="text-muted">Max 5MB. PDF, DOC, DOCX, JPG, PNG allowed.</small>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-send"></i> Submit Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="bi bi-list-check"></i> Leave History</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($leave_requests)): ?>
                                    <p class="text-muted">No leave requests yet.</p>
                                <?php else: ?>
                                    <?php foreach($leave_requests as $leave): ?>
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><?php echo htmlspecialchars($leave['leave_type']); ?></strong>
                                                <div class="small text-muted">
                                                    <?php echo date('M d', strtotime($leave['start_date'])); ?> - 
                                                    <?php echo date('M d, Y', strtotime($leave['end_date'])); ?>
                                                </div>
                                                <div class="small"><?php echo htmlspecialchars($leave['reason']); ?></div>
                                            </div>
                                            <span class="status-badge status-<?php echo strtolower($leave['status']); ?>">
                                                <?php echo htmlspecialchars($leave['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Info Tab -->
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-person-gear"></i> Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="employee_actions.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($employee['name'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($employee['email'] ?? $user['email']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" class="form-control" name="birthday" value="<?php echo htmlspecialchars($employee['birthday'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile Number</label>
                                    <input type="tel" class="form-control" name="mobile" value="<?php echo htmlspecialchars($employee['mobile'] ?? ''); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php echo ($employee['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($employee['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($employee['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Civil Status</label>
                                    <select class="form-select" name="civil_status">
                                        <option value="">Select Status</option>
                                        <option value="Single" <?php echo ($employee['civil_status'] ?? '') == 'Single' ? 'selected' : ''; ?>>Single</option>
                                        <option value="Married" <?php echo ($employee['civil_status'] ?? '') == 'Married' ? 'selected' : ''; ?>>Married</option>
                                        <option value="Divorced" <?php echo ($employee['civil_status'] ?? '') == 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                                        <option value="Widowed" <?php echo ($employee['civil_status'] ?? '') == 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control mb-2" name="unit_building" placeholder="Unit/Building" value="<?php echo htmlspecialchars($employee['unit_building'] ?? ''); ?>">
                                <input type="text" class="form-control mb-2" name="street" placeholder="Street Name" value="<?php echo htmlspecialchars($employee['street'] ?? ''); ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="city" placeholder="City" value="<?php echo htmlspecialchars($employee['city'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="province" placeholder="Province" value="<?php echo htmlspecialchars($employee['province'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="zipcode" placeholder="Zip Code" value="<?php echo htmlspecialchars($employee['zipcode'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payslips Tab -->
            <div class="tab-pane fade" id="payslips" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-receipt"></i> Payslip History</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($payslips)): ?>
                            <p class="text-muted">No payslips available yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
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
                                            <td><?php echo date('M Y', strtotime($payslip['pay_period'])); ?></td>
                                            <td>₱<?php echo number_format($payslip['basic_salary'], 2); ?></td>
                                            <td>₱<?php echo number_format($payslip['allowances'], 2); ?></td>
                                            <td>₱<?php echo number_format($payslip['deductions'] + $payslip['tax'], 2); ?></td>
                                            <td><strong>₱<?php echo number_format($payslip['net_pay'], 2); ?></strong></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary view-payslip" 
                                                        data-id="<?php echo $payslip['id']; ?>"
                                                        data-period="<?php echo date('M Y', strtotime($payslip['pay_period'])); ?>"
                                                        data-basic="<?php echo number_format($payslip['basic_salary'], 2); ?>"
                                                        data-allowances="<?php echo number_format($payslip['allowances'], 2); ?>"
                                                        data-deductions="<?php echo number_format($payslip['deductions'], 2); ?>"
                                                        data-tax="<?php echo number_format($payslip['tax'], 2); ?>"
                                                        data-net="<?php echo number_format($payslip['net_pay'], 2); ?>"
                                                        data-bs-toggle="modal" data-bs-target="#payslipModal">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payslip Detail Modal -->
    <div class="modal fade" id="payslipModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payslip Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <h6>Starbucks Corporation</h6>
                        <p class="mb-1"><?php echo htmlspecialchars($employee['name'] ?? $user['email']); ?></p>
                        <p class="mb-1"><?php echo htmlspecialchars($employee['email'] ?? $user['email']); ?></p>
                        <p><strong>Pay Period: <span id="modal-period"></span></strong></p>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <td>Basic Salary:</td>
                            <td class="text-end">₱<span id="modal-basic"></span></td>
                        </tr>
                        <tr>
                            <td>Allowances:</td>
                            <td class="text-end">₱<span id="modal-allowances"></span></td>
                        </tr>
                        <tr>
                            <td>Deductions:</td>
                            <td class="text-end">₱<span id="modal-deductions"></span></td>
                        </tr>
                        <tr>
                            <td>Tax:</td>
                            <td class="text-end">₱<span id="modal-tax"></span></td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Net Pay:</strong></td>
                            <td class="text-end"><strong>₱<span id="modal-net"></span></strong></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // View payslip modal
        document.querySelectorAll('.view-payslip').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal-period').textContent = this.dataset.period;
                document.getElementById('modal-basic').textContent = this.dataset.basic;
                document.getElementById('modal-allowances').textContent = this.dataset.allowances;
                document.getElementById('modal-deductions').textContent = this.dataset.deductions;
                document.getElementById('modal-tax').textContent = this.dataset.tax;
                document.getElementById('modal-net').textContent = this.dataset.net;
            });
        });
    </script>
</body>
</html>
