<?php
session_start();
include 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Access denied.");
}

// Handle file download
if (isset($_GET['download_doc'])) {
    $doc_id = intval($_GET['download_doc']);
    $stmt = $conn->prepare("SELECT * FROM application_documents WHERE id = ?");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();
    $stmt->close();
    
    if ($document && file_exists($document['file_path'])) {
        header('Content-Type: ' . $document['file_type']);
        header('Content-Disposition: attachment; filename="' . $document['original_filename'] . '"');
        header('Content-Length: ' . filesize($document['file_path']));
        readfile($document['file_path']);
        exit();
    } else {
        $_SESSION['error'] = "File not found.";
        header('Location: admin_dashboard_enhanced.php#applications');
        exit();
    }
}

// Employee Management Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Add Employee
    if ($_POST['action'] === 'add_employee') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $department = $_POST['department'];
        $hire_date = $_POST['hire_date'];
        $salary = $_POST['salary'];
        $status = $_POST['status'] ?? 'Active';
        
        $conn->begin_transaction();
        try {
            // Create user account
            $password = password_hash('password123', PASSWORD_DEFAULT);
            $stmt_user = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'employee')");
            $stmt_user->bind_param("ss", $email, $password);
            $stmt_user->execute();
            
            // Create employee record
            $stmt = $conn->prepare("INSERT INTO employees (name, email, position, department, hire_date, salary, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssds", $name, $email, $position, $department, $hire_date, $salary, $status);
            $stmt->execute();
            
            $conn->commit();
            $_SESSION['success'] = "Employee added successfully. Default password: password123";
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $_SESSION['error'] = "Error adding employee: " . $exception->getMessage();
        }
    }
    
    // Update Employee
    if ($_POST['action'] === 'update_employee') {
        $id = intval($_POST['employee_id']);
        $name = $_POST['name'];
        $email = $_POST['email'];
        $position = $_POST['position'];
        $department = $_POST['department'];
        $salary = $_POST['salary'];
        
        $stmt = $conn->prepare("UPDATE employees SET name=?, email=?, position=?, department=?, salary=? WHERE id=?");
        $stmt->bind_param("ssssdi", $name, $email, $position, $department, $salary, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Employee updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating employee: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Archive Employee
    if ($_POST['action'] === 'archive_employee') {
        $id = intval($_POST['employee_id']);
        $stmt = $conn->prepare("UPDATE employees SET archived=1 WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Employee archived successfully.";
        } else {
            $_SESSION['error'] = "Error archiving employee: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Update Employment Status
    if ($_POST['action'] === 'update_status') {
        $id = intval($_POST['employee_id']);
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE employees SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Employee status updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating status: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Application Management Actions
    if ($_POST['action'] === 'update_application_status') {
        $id = intval($_POST['application_id']);
        $status = $_POST['status'];
        $allowed_statuses = ['Pending', 'Reviewed', 'Shortlisted', 'Interview Scheduled', 'Accepted', 'Rejected'];
        
        if (in_array($status, $allowed_statuses)) {
            $conn->begin_transaction();
            try {
                // Update application status
                $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $id);
                $stmt->execute();
                
                // If accepted, create employee record
                if ($status === 'Accepted') {
                    // Get application details
                    $stmt = $conn->prepare("SELECT a.*, j.title as job_title, j.department FROM applications a JOIN jobs j ON a.job_id = j.id WHERE a.id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $app_result = $stmt->get_result();
                    $application = $app_result->fetch_assoc();
                    
                    if ($application) {
                        // Check if user account already exists
                        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt_check->bind_param("s", $application['applicant_email']);
                        $stmt_check->execute();
                        $user_exists = $stmt_check->get_result()->fetch_assoc();
                        $stmt_check->close();
                        
                        if (!$user_exists) {
                            // Create user account
                            $password = password_hash('password123', PASSWORD_DEFAULT);
                            $stmt_user = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, 'employee')");
                            $stmt_user->bind_param("ss", $application['applicant_email'], $password);
                            $stmt_user->execute();
                        }
                        
                        // Check if employee record already exists
                        $stmt_check_emp = $conn->prepare("SELECT id FROM employees WHERE email = ?");
                        $stmt_check_emp->bind_param("s", $application['applicant_email']);
                        $stmt_check_emp->execute();
                        $emp_exists = $stmt_check_emp->get_result()->fetch_assoc();
                        $stmt_check_emp->close();
                        
                        if (!$emp_exists) {
                            // Create employee record
                            $salary = 25000.00; // Default salary
                            $stmt_emp = $conn->prepare("INSERT INTO employees (name, email, position, department, hire_date, salary, status) VALUES (?, ?, ?, ?, CURDATE(), ?, 'Active')");
                            $stmt_emp->bind_param("ssssd", $application['applicant_name'], $application['applicant_email'], $application['job_title'], $application['department'], $salary);
                            $stmt_emp->execute();
                        }
                    }
                }
                
                $conn->commit();
                $_SESSION['success'] = "Application status updated successfully." . ($status === 'Accepted' ? ' Employee record created.' : '');
            } catch (mysqli_sql_exception $exception) {
                $conn->rollback();
                $_SESSION['error'] = "Error updating application status: " . $exception->getMessage();
            }
        }
    }
    
    // Schedule Interview
    if ($_POST['action'] === 'schedule_interview') {
        $application_id = intval($_POST['application_id']);
        $interview_date = $_POST['interview_date'];
        $interview_time = $_POST['interview_time'];
        $interview_location = $_POST['interview_location'];
        $interviewer = $_POST['interviewer'];
        $interview_notes = $_POST['interview_notes'] ?? '';
        
        // Create interview_schedules table if it doesn't exist
        $conn->query("CREATE TABLE IF NOT EXISTS interview_schedules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            application_id INT NOT NULL,
            interview_date DATE NOT NULL,
            interview_time TIME NOT NULL,
            location VARCHAR(255) NOT NULL,
            interviewer VARCHAR(255) NOT NULL,
            notes TEXT,
            status ENUM('Scheduled', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
        )");
        
        $stmt = $conn->prepare("INSERT INTO interview_schedules (application_id, interview_date, interview_time, location, interviewer, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $application_id, $interview_date, $interview_time, $interview_location, $interviewer, $interview_notes);
        
        if ($stmt->execute()) {
            // Update application status to Interview Scheduled
            $stmt_update = $conn->prepare("UPDATE applications SET status = 'Interview Scheduled' WHERE id = ?");
            $stmt_update->bind_param("i", $application_id);
            $stmt_update->execute();
            $stmt_update->close();
            
            $_SESSION['success'] = "Interview scheduled successfully.";
        } else {
            $_SESSION['error'] = "Error scheduling interview: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Job Management Actions
    if ($_POST['action'] === 'add_job') {
        $title = $_POST['title'];
        $department = $_POST['department'];
        $location = $_POST['location'];
        $job_type = $_POST['job_type'];
        $pay = $_POST['pay'];
        $description = $_POST['description'];
        $qualifications = $_POST['qualifications'];
        $status = $_POST['status'] ?? 'Open';
        
        $stmt = $conn->prepare("INSERT INTO jobs (title, department, location, job_type, pay, description, qualifications, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $title, $department, $location, $job_type, $pay, $description, $qualifications, $status);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Job opening added successfully.";
        } else {
            $_SESSION['error'] = "Error adding job: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Update Job
    if ($_POST['action'] === 'update_job') {
        $id = intval($_POST['job_id']);
        $title = $_POST['title'];
        $department = $_POST['department'];
        $location = $_POST['location'];
        $job_type = $_POST['job_type'];
        $pay = $_POST['pay'];
        $description = $_POST['description'];
        $qualifications = $_POST['qualifications'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE jobs SET title=?, department=?, location=?, job_type=?, pay=?, description=?, qualifications=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $title, $department, $location, $job_type, $pay, $description, $qualifications, $status, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Job updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating job: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Delete Job
    if ($_POST['action'] === 'delete_job') {
        $id = intval($_POST['job_id']);
        $stmt = $conn->prepare("DELETE FROM jobs WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Job deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting job: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Leave Management Actions
    if ($_POST['action'] === 'process_leave') {
        $id = intval($_POST['leave_id']);
        $status = $_POST['leave_status'];
        $admin_notes = $_POST['admin_notes'] ?? '';
        
        $stmt = $conn->prepare("UPDATE leaves SET status=?, admin_notes=?, processed_at=NOW() WHERE id=?");
        $stmt->bind_param("ssi", $status, $admin_notes, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Leave request processed successfully.";
        } else {
            $_SESSION['error'] = "Error processing leave: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Payslip Management Actions
    if ($_POST['action'] === 'generate_payslip') {
        $employee_id = intval($_POST['employee_id']);
        $pay_period = $_POST['pay_period'];
        $basic_salary = floatval($_POST['basic_salary']);
        $allowances = floatval($_POST['allowances']);
        $deductions = floatval($_POST['deductions']);
        $tax = floatval($_POST['tax']);
        $net_pay = $basic_salary + $allowances - $deductions - $tax;
        
        $stmt = $conn->prepare("INSERT INTO payslips (employee_id, pay_period, basic_salary, allowances, deductions, tax, net_pay) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isddddd", $employee_id, $pay_period, $basic_salary, $allowances, $deductions, $tax, $net_pay);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Payslip generated successfully.";
        } else {
            $_SESSION['error'] = "Error generating payslip: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Update Payslip
    if ($_POST['action'] === 'update_payslip') {
        $payslip_id = intval($_POST['payslip_id']);
        $pay_period = $_POST['pay_period'];
        $basic_salary = floatval($_POST['basic_salary']);
        $allowances = floatval($_POST['allowances']);
        $deductions = floatval($_POST['deductions']);
        $tax = floatval($_POST['tax']);
        $net_pay = $basic_salary + $allowances - $deductions - $tax;
        
        $stmt = $conn->prepare("UPDATE payslips SET pay_period=?, basic_salary=?, allowances=?, deductions=?, tax=?, net_pay=? WHERE id=?");
        $stmt->bind_param("sdddddi", $pay_period, $basic_salary, $allowances, $deductions, $tax, $net_pay, $payslip_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Payslip updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating payslip: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Announcement Management Actions
    if ($_POST['action'] === 'add_announcement') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = $_POST['date'];
        
        $stmt = $conn->prepare("INSERT INTO announcements (title, content, date, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $title, $content, $date);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement added successfully.";
        } else {
            $_SESSION['error'] = "Error adding announcement: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Update Announcement
    if ($_POST['action'] === 'update_announcement') {
        $id = intval($_POST['announcement_id']);
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = $_POST['date'];
        
        $stmt = $conn->prepare("UPDATE announcements SET title=?, content=?, date=? WHERE id=?");
        $stmt->bind_param("sssi", $title, $content, $date, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating announcement: " . $stmt->error;
        }
        $stmt->close();
    }
    
    // Delete Announcement
    if ($_POST['action'] === 'delete_announcement') {
        $id = intval($_POST['announcement_id']);
        $stmt = $conn->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Announcement deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting announcement: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Redirect back to dashboard
header('Location: admin_dashboard_enhanced.php');
exit();
?>