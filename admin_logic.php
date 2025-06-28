<?php
session_start();
include 'config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Helper function to check if column exists
function column_exists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $result && $result->num_rows > 0;
}

// Helper function to check if table exists
function table_exists($conn, $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    return $result && $result->num_rows > 0;
}

// Fetch applications data
function fetch_applications($conn) {
    if (!table_exists($conn, 'applications') || !table_exists($conn, 'jobs')) {
        return [];
    }
    
    $sql = "SELECT applications.*, jobs.title as job_title";
    
    // Check if application_documents table exists
    if (table_exists($conn, 'application_documents')) {
        $sql .= ", application_documents.file_path, application_documents.original_filename, application_documents.file_type
                FROM applications 
                JOIN jobs ON applications.job_id = jobs.id 
                LEFT JOIN application_documents ON applications.id = application_documents.application_id";
    } else {
        $sql .= " FROM applications 
                JOIN jobs ON applications.job_id = jobs.id";
    }
    
    // Check if applied_at column exists
    if (column_exists($conn, 'applications', 'applied_at')) {
        $sql .= " ORDER BY applications.applied_at DESC";
    } else {
        $sql .= " ORDER BY applications.id DESC";
    }
    
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch employees data
function fetch_employees($conn) {
    if (!table_exists($conn, 'employees')) {
        return [];
    }
    
    $sql = "SELECT * FROM employees";
    
    // Check if archived column exists
    if (column_exists($conn, 'employees', 'archived')) {
        $sql .= " WHERE archived = 0";
    }
    
    $sql .= " ORDER BY name";
    
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch jobs from database
function fetch_jobs($conn) {
    if (!table_exists($conn, 'jobs')) {
        return [];
    }
    
    $sql = "SELECT * FROM jobs ORDER BY id DESC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch announcements from database with fallback ordering
function fetch_announcements($conn) {
    if (!table_exists($conn, 'announcements')) {
        return [];
    }
    
    // Check if created_at column exists
    if (column_exists($conn, 'announcements', 'created_at')) {
        $sql = "SELECT * FROM announcements ORDER BY created_at DESC";
    } else {
        $sql = "SELECT * FROM announcements ORDER BY date DESC";
    }
    
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch leave requests
function fetch_leave_requests($conn) {
    if (!table_exists($conn, 'leaves') || !table_exists($conn, 'employees')) {
        return [];
    }
    
    $sql = "SELECT l.*, e.name as employee_name, e.email as employee_email 
            FROM leaves l 
            JOIN employees e ON l.employee_id = e.id";
    
    // Check if created_at column exists in leaves table
    if (column_exists($conn, 'leaves', 'created_at')) {
        $sql .= " ORDER BY l.created_at DESC";
    } else {
        $sql .= " ORDER BY l.id DESC";
    }
    
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// Fetch payslips
function fetch_payslips($conn) {
    if (!table_exists($conn, 'payslips') || !table_exists($conn, 'employees')) {
        return [];
    }
    
    $sql = "SELECT p.*, e.name as employee_name, e.email as employee_email 
            FROM payslips p 
            JOIN employees e ON p.employee_id = e.id";
    
    // Check if created_at column exists in payslips table
    if (column_exists($conn, 'payslips', 'created_at')) {
        $sql .= " ORDER BY p.created_at DESC";
    } else {
        $sql .= " ORDER BY p.pay_period DESC";
    }
    
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$applications = fetch_applications($conn);
$employees = fetch_employees($conn);
$jobs = fetch_jobs($conn);
$announcements = fetch_announcements($conn);
$leave_requests = fetch_leave_requests($conn);
$payslips = fetch_payslips($conn);
?>
