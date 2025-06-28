<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$user = $_SESSION['user'];

// Get employee ID
$stmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$employee_id = $employee ? $employee['id'] : 0;
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle Leave Request
    if ($_POST['action'] === 'request_leave') {
        if ($employee_id > 0) {
            $leave_type = $_POST['leave_type'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $reason = $_POST['reason'];
            
            // Handle file upload if present
            $document_path = null;
            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/leave_documents/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
                $new_filename = 'leave_' . $employee_id . '_' . time() . '.' . $file_extension;
                $document_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['document']['tmp_name'], $document_path)) {
                    // File uploaded successfully
                } else {
                    $document_path = null;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO leaves (employee_id, leave_type, start_date, end_date, reason, document_path, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
            $stmt->bind_param("isssss", $employee_id, $leave_type, $start_date, $end_date, $reason, $document_path);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Leave request submitted successfully.";
            } else {
                $_SESSION['error'] = "Error submitting request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Employee record not found.";
        }
    }
    
    // Handle Profile Update
    if ($_POST['action'] === 'update_profile') {
        if ($employee_id > 0) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $birthday = $_POST['birthday'];
            $mobile = $_POST['mobile'];
            $gender = $_POST['gender'];
            $civil_status = $_POST['civil_status'];
            $unit_building = $_POST['unit_building'];
            $street = $_POST['street'];
            $city = $_POST['city'];
            $province = $_POST['province'];
            $zipcode = $_POST['zipcode'];
            
            $sql = "UPDATE employees SET name=?, email=?, birthday=?, mobile=?, gender=?, civil_status=?, unit_building=?, street=?, city=?, province=?, zipcode=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssssi", $name, $email, $birthday, $mobile, $gender, $civil_status, $unit_building, $street, $city, $province, $zipcode, $employee_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Profile updated successfully.";
                // Update session email if changed
                if ($email !== $user['email']) {
                    $_SESSION['user']['email'] = $email;
                }
            } else {
                $_SESSION['error'] = "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Employee record not found.";
        }
    }
}

header('Location: employee_portal.php');
exit();
?>