<?php
// Redirect to the enhanced admin dashboard
header('Location: admin_dashboard_enhanced.php');
exit();
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
    body { background: #f0f8f0; }
    .sidebar {
      min-height: 100vh;
      background: #006241;
      color: #fff;
      padding-top: 30px;
    }
    .sidebar .nav-link { color: #fff; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background: #004d34; color: #fff; }
    .section-title { color: #006241; font-weight: bold; margin-top: 20px; }
    .card { margin-bottom: 24px; border: 1px solid #e8f5e9; }
    .table { border-collapse: separate; border-spacing: 0; }
    .table thead { background: #006241; color: white; }
    .table th, .table td { vertical-align: middle; }
    .table tbody tr:hover { background-color: #e8f5e9; }
    .btn-primary { background-color: #006241; border-color: #006241; }
    .btn-success { background-color: #006241; border-color: #006241; }
    .status-badge {
      padding: 0.25rem 0.5rem;
      border-radius: 0.25rem;
      font-size: 0.75rem;
      font-weight: 500;
    }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-approved { background-color: #d4edda; color: #155724; }
    .status-rejected { background-color: #f8d7da; color: #721c24; }
    .alert { margin-bottom: 1rem; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <nav class="col-md-2 d-none d-md-block sidebar">
        <div class="position-sticky">
          <h5 class="px-3 mb-3">Admin Panel</h5>
          <ul class="nav flex-column" id="adminNav">
            <li class="nav-item"><a class="nav-link active" href="#applications">Applications</a></li>
            <li class="nav-item"><a class="nav-link" href="#employees">Employees</a></li>
            <li class="nav-item"><a class="nav-link" href="#jobs">Job Openings</a></li>
            <li class="nav-item"><a class="nav-link" href="#leaves">Leave Requests</a></li>
            <li class="nav-item"><a class="nav-link" href="#payslips">Payslips</a></li>
            <li class="nav-item"><a class="nav-link" href="#announcements">Announcements</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          </ul>
        </div>
      </nav>
      
      <!-- Main Content -->
      <main class="col-md-10 ms-sm-auto px-4">
        <div class="d-flex justify-content-between align-items-center my-4">
          <h1>Admin Dashboard</h1>
          <div class="text-muted">Welcome, <?php echo htmlspecialchars($_SESSION['user']['email']); ?></div>
        </div>
        
        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        
        <!-- Applications Section -->
        <section id="applications">
          <h3 class="section-title">Employee Applications</h3>
          <div class="card p-3">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Applicant Name</th>
                  <th>Email</th>
                  <th>Applied for</th>
                  <th>Status</th>
                  <th>Applied Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($applications)): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">No applications received yet</td>
                </tr>
                <?php else: ?>
                  <?php foreach ($applications as $application): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($application['applicant_name']); ?></td>
                    <td><?php echo htmlspecialchars($application['applicant_email']); ?></td>
                    <td><?php echo htmlspecialchars($application['job_title']); ?></td>
                    <td>
                      <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $application['status'])); ?>">
                        <?php echo htmlspecialchars($application['status']); ?>
                      </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($application['applied_at'])); ?></td>
                    <td>
                      <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                          Update Status
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="admin_actions.php?update_app_status=<?php echo $application['id']; ?>&status=Reviewed">Mark as Reviewed</a></li>
                          <li><a class="dropdown-item" href="admin_actions.php?update_app_status=<?php echo $application['id']; ?>&status=Shortlisted">Shortlist</a></li>
                          <li><a class="dropdown-item" href="admin_actions.php?update_app_status=<?php echo $application['id']; ?>&status=Interview Scheduled">Schedule Interview</a></li>
                          <li><a class="dropdown-item text-success" href="admin_actions.php?update_app_status=<?php echo $application['id']; ?>&status=Accepted">Accept</a></li>
                          <li><a class="dropdown-item text-danger" href="admin_actions.php?update_app_status=<?php echo $application['id']; ?>&status=Rejected">Reject</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Employee Management Section -->
        <section id="employees" style="display: none;">
          <h3 class="section-title">Employee Management</h3>
          <div class="card p-3">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
              <i class="bi bi-plus-circle"></i> Add Employee
            </button>
            
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Position</th>
                  <th>Department</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($employees as $employee): ?>
                <tr>
                  <td><?= htmlspecialchars($employee['name']) ?></td>
                  <td><?= htmlspecialchars($employee['email']) ?></td>
                  <td><?= htmlspecialchars($employee['position']) ?></td>
                  <td><?= htmlspecialchars($employee['department']) ?></td>
                  <td>
                    <form method="post" action="admin_actions.php" class="d-inline">
                      <input type="hidden" name="action" value="update_status">
                      <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                      <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="Active" <?= $employee['status'] == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="On Leave" <?= $employee['status'] == 'On Leave' ? 'selected' : '' ?>>On Leave</option>
                        <option value="Resigned" <?= $employee['status'] == 'Resigned' ? 'selected' : '' ?>>Resigned</option>
                        <option value="Terminated" <?= $employee['status'] == 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                      </select>
                    </form>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-primary edit-employee" 
                            data-id="<?= $employee['id'] ?>"
                            data-name="<?= htmlspecialchars($employee['name']) ?>"
                            data-email="<?= htmlspecialchars($employee['email']) ?>"
                            data-position="<?= htmlspecialchars($employee['position']) ?>"
                            data-department="<?= htmlspecialchars($employee['department']) ?>"
                            data-salary="<?= $employee['salary'] ?>"
                            data-bs-toggle="modal" data-bs-target="#editEmployeeModal">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <form method="post" action="admin_actions.php" class="d-inline">
                      <input type="hidden" name="action" value="archive_employee">
                      <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Archive this employee?')">
                        <i class="bi bi-archive"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Job Management Section -->
        <section id="jobs" style="display: none;">
          <h3 class="section-title">Job Management</h3>
          <div class="card p-3">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addJobModal">
              <i class="bi bi-plus-circle"></i> Add Job Opening
            </button>
            
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Department</th>
                  <th>Location</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($jobs as $job): ?>
                <tr>
                  <td><?= htmlspecialchars($job['title']) ?></td>
                  <td><?= htmlspecialchars($job['department']) ?></td>
                  <td><?= htmlspecialchars($job['location']) ?></td>
                  <td><?= htmlspecialchars($job['job_type']) ?></td>
                  <td>
                    <span class="badge <?= $job['status'] == 'Open' ? 'bg-success' : 'bg-secondary' ?>">
                      <?= htmlspecialchars($job['status']) ?>
                    </span>
                  </td>
                  <td>
                    <form method="post" action="admin_actions.php" class="d-inline">
                      <input type="hidden" name="action" value="delete_job">
                      <input type="hidden" name="job_id" value="<?= $job['id'] ?>">
                      <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this job?')">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Leave Management Section -->
        <section id="leaves" style="display: none;">
          <h3 class="section-title">Leave Management</h3>
          <div class="card p-3">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Employee</th>
                  <th>Leave Type</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($leave_requests as $leave): ?>
                <tr>
                  <td><?= htmlspecialchars($leave['employee_name']) ?></td>
                  <td><?= htmlspecialchars($leave['leave_type']) ?></td>
                  <td><?= htmlspecialchars($leave['start_date']) ?></td>
                  <td><?= htmlspecialchars($leave['end_date']) ?></td>
                  <td>
                    <span class="status-badge status-<?= strtolower($leave['status']) ?>">
                      <?= htmlspecialchars($leave['status']) ?>
                    </span>
                  </td>
                  <td>
                    <?php if ($leave['status'] == 'Pending'): ?>
                    <form method="post" action="admin_actions.php" class="d-inline">
                      <input type="hidden" name="action" value="process_leave">
                      <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">
                      <input type="hidden" name="leave_status" value="Approved">
                      <button type="submit" class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form method="post" action="admin_actions.php" class="d-inline">
                      <input type="hidden" name="action" value="process_leave">
                      <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">
                      <input type="hidden" name="leave_status" value="Rejected">
                      <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                    </form>
                    <?php else: ?>
                    <span class="text-muted">Processed</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Payslip Management Section -->
        <section id="payslips" style="display: none;">
          <h3 class="section-title">Payslip Management</h3>
          <div class="card p-3">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#generatePayslipModal">
              <i class="bi bi-plus-circle"></i> Generate Payslip
            </button>
            
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Employee</th>
                  <th>Pay Period</th>
                  <th>Basic Salary</th>
                  <th>Net Pay</th>
                  <th>Generated</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($payslips as $payslip): ?>
                <tr>
                  <td><?= htmlspecialchars($payslip['employee_name']) ?></td>
                  <td><?= htmlspecialchars($payslip['pay_period']) ?></td>
                  <td>₱<?= number_format($payslip['basic_salary'], 2) ?></td>
                  <td>₱<?= number_format($payslip['net_pay'], 2) ?></td>
                  <td><?= date('M d, Y', strtotime($payslip['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Announcement Management Section -->
        <section id="announcements" style="display: none;">
          <h3 class="section-title">Announcements</h3>
          <div class="card p-3">
            <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
              <i class="bi bi-plus-circle"></i> Add Announcement
            </button>
            
            <div class="row">
              <?php foreach ($announcements as $announcement): ?>
              <div class="col-md-6 mb-3">
                <div class="card border">
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                      <h5 class="card-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                      <form method="post" action="admin_actions.php" class="d-inline">
                        <input type="hidden" name="action" value="delete_announcement">
                        <input type="hidden" name="announcement_id" value="<?= $announcement['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this announcement?')">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                    <small class="text-muted"><?php echo date('M d, Y', strtotime($announcement['date'])); ?></small>
                    <p class="card-text mt-2"><?php echo htmlspecialchars($announcement['content']); ?></p>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>

  <!-- Modals -->
  <!-- Add Employee Modal -->
  <div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="admin_actions.php">
          <div class="modal-body">
            <input type="hidden" name="action" value="add_employee">
            <div class="mb-3">
              <label class="form-label">Name</label>
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
              <input type="text" class="form-control" name="department" required>
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
        <div class="modal-header">
          <h5 class="modal-title">Edit Employee</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="admin_actions.php">
          <div class="modal-body">
            <input type="hidden" name="action" value="update_employee">
            <input type="hidden" name="employee_id" id="edit_employee_id">
            <div class="mb-3">
              <label class="form-label">Name</label>
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
              <input type="text" class="form-control" name="department" id="edit_department" required>
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

  <!-- Add Job Modal -->
  <div class="modal fade" id="addJobModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Job Opening</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="admin_actions.php">
          <div class="modal-body">
            <input type="hidden" name="action" value="add_job">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Job Title</label>
                <input type="text" class="form-control" name="title" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Department</label>
                <input type="text" class="form-control" name="department" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" name="location" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Job Type</label>
                <select class="form-control" name="job_type" required>
                  <option value="Full-time">Full-time</option>
                  <option value="Part-time">Part-time</option>
                  <option value="Contract">Contract</option>
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Pay</label>
              <input type="text" class="form-control" name="pay" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
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

  <!-- Generate Payslip Modal -->
  <div class="modal fade" id="generatePayslipModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Generate Payslip</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="admin_actions.php">
          <div class="modal-body">
            <input type="hidden" name="action" value="generate_payslip">
            <div class="mb-3">
              <label class="form-label">Employee</label>
              <select class="form-control" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php foreach($employees as $emp): ?>
                <option value="<?= $emp['id'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
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

  <!-- Add Announcement Modal -->
  <div class="modal fade" id="addAnnouncementModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="post" action="admin_actions.php">
          <div class="modal-body">
            <input type="hidden" name="action" value="add_announcement">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" name="title" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Content</label>
              <textarea class="form-control" name="content" rows="4" required></textarea>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar navigation
    document.querySelectorAll('#adminNav .nav-link').forEach(function(link) {
      link.addEventListener('click', function(e) {
        if (link.getAttribute('href') === 'logout.php') return;
        
        e.preventDefault();
        
        // Hide all sections
        document.querySelectorAll('section').forEach(section => {
          section.style.display = 'none';
        });
        
        // Show target section
        const targetId = link.getAttribute('href').substring(1);
        const targetSection = document.getElementById(targetId);
        if (targetSection) {
          targetSection.style.display = 'block';
        }
        
        // Update active nav
        document.querySelectorAll('#adminNav .nav-link').forEach(l => l.classList.remove('active'));
        link.classList.add('active');
      });
    });

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
  </script>
</body>
</html>
