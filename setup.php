<?php
include 'config.php';

// SQL to drop existing tables
$sql_drop = "
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS users, employees, jobs, applications, payroll, leaves, announcements, documents, performance, reports, application_documents, application_notes;
SET FOREIGN_KEY_CHECKS = 1;
";

if ($conn->multi_query($sql_drop)) {
    echo "Old tables dropped successfully.<br>";
    // Clear any remaining results from multi_query
    while ($conn->next_result()) {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    }
} else {
    echo "Error dropping tables: " . $conn->error . "<br>";
}


// SQL to create tables
$sql_create = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee', 'manager') NOT NULL
);

CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('Active', 'On Leave', 'Resigned', 'Terminated') NOT NULL
);

CREATE TABLE IF NOT EXISTS jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    qualifications TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    department VARCHAR(255) NOT NULL,
    pay VARCHAR(100) NOT NULL,
    job_type VARCHAR(50) NOT NULL,
    deadline DATE,
    status ENUM('Open', 'Closed') NOT NULL DEFAULT 'Open'
);

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_name VARCHAR(255) NOT NULL,
    applicant_email VARCHAR(255) NOT NULL,
    applicant_phone VARCHAR(20),
    status ENUM('Pending', 'Reviewed', 'Shortlisted', 'Interview Scheduled', 'Accepted', 'Rejected') NOT NULL DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS application_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255) NOT NULL,
    file_type VARCHAR(100) NOT NULL,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS application_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    note TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    salary DECIMAL(10, 2) NOT NULL,
    tax_info VARCHAR(255) NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    leave_type VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS performance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    evaluation TEXT NOT NULL,
    award VARCHAR(255),
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_name VARCHAR(255) NOT NULL,
    summary TEXT,
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if ($conn->multi_query($sql_create)) {
    echo "Tables created successfully<br>";
} else {
    echo "Error creating tables: " . $conn->error . "<br>";
}

// Clear any remaining results from multi_query
while ($conn->next_result()) {
    if ($result = $conn->store_result()) {
        $result->free();
    }
}

// Insert hardcoded jobs
$jobs = [
    [
        'id' => 1,
        'title' => 'Barista',
        'location' => 'Downtown Store',
        'department' => 'Store Operations',
        'pay' => '$15-18/hour',
        'job_type' => 'Part-time',
        'status' => 'Open',
        'description' => 'Join our team as a Barista and be part of creating the Starbucks Experience. You will be responsible for crafting beverages, connecting with customers, and maintaining store cleanliness. This role offers flexible scheduling and opportunities for growth within our organization.

Key Responsibilities:
• Prepare and serve beverages according to Starbucks standards
• Maintain store cleanliness and organization
• Provide excellent customer service
• Work collaboratively with team members
• Follow food safety and sanitation guidelines

This position offers flexible scheduling, competitive pay, and opportunities for advancement within our organization.',
        'qualifications' => 'High school diploma, Customer service experience, Ability to work in a fast-paced environment, Team player, Flexible schedule availability',
        'deadline' => '2024-12-31'
    ],
    [
        'id' => 2,
        'title' => 'Shift Supervisor',
        'location' => 'Mall Location',
        'department' => 'Store Operations',
        'pay' => '$18-22/hour',
        'job_type' => 'Full-time',
        'status' => 'Open',
        'description' => 'As a Shift Supervisor, you will lead store operations during your shift, ensuring excellent customer service and team performance. You will train and coach baristas, manage inventory, and maintain store standards. This position offers leadership development and career advancement opportunities.

Key Responsibilities:
• Lead store operations during assigned shifts
• Train and coach team members
• Manage inventory and ordering
• Ensure store cleanliness and safety standards
• Handle customer concerns and complaints
• Support store manager in achieving business goals

This role provides leadership experience and opportunities for advancement to Store Manager positions.',
        'qualifications' => 'Previous Starbucks experience preferred, Leadership experience, Strong communication skills, Problem-solving abilities, Availability for various shifts',
        'deadline' => '2024-12-31'
    ],
    [
        'id' => 3,
        'title' => 'Store Manager',
        'location' => 'University Plaza',
        'department' => 'Management',
        'pay' => '$45,000-55,000/year',
        'job_type' => 'Full-time',
        'status' => 'Open',
        'description' => 'Lead a team of passionate partners in creating the Starbucks Experience. As a Store Manager, you will oversee all aspects of store operations including team development, financial performance, customer satisfaction, and community engagement. This role offers competitive benefits and opportunities for regional advancement.

Key Responsibilities:
• Lead and develop a team of 15-25 partners
• Manage store financial performance and budgets
• Ensure exceptional customer experience
• Oversee inventory management and ordering
• Implement company policies and procedures
• Build relationships with the local community
• Drive sales and operational excellence

This position offers competitive salary, comprehensive benefits, and opportunities for regional advancement.',
        'qualifications' => 'Bachelor\'s degree preferred, 3+ years retail management experience, Strong leadership and communication skills, Financial acumen, Customer-focused mindset',
        'deadline' => '2024-12-31'
    ],
    [
        'id' => 4,
        'title' => 'Customer Service Representative',
        'location' => 'Airport Location',
        'department' => 'Customer Service',
        'pay' => '$16-20/hour',
        'job_type' => 'Full-time',
        'status' => 'Open',
        'description' => 'Serve customers at our high-traffic airport location. You will provide exceptional service to travelers, handle cash transactions, and maintain a welcoming environment. This position requires flexibility with scheduling and the ability to work in a dynamic airport setting.

Key Responsibilities:
• Provide exceptional customer service to travelers
• Handle cash and card transactions accurately
• Maintain store cleanliness and organization
• Work efficiently in a high-traffic environment
• Follow airport security protocols
• Collaborate with airport staff and security

This position offers competitive pay, flexible scheduling, and the opportunity to serve customers from around the world.',
        'qualifications' => 'Customer service experience, Cash handling skills, Ability to work flexible hours, Airport security clearance required, Strong interpersonal skills',
        'deadline' => '2024-12-31'
    ]
];

// Insert jobs into database
foreach ($jobs as $job) {
    $stmt = $conn->prepare("INSERT INTO jobs (id, title, description, qualifications, location, department, pay, job_type, deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssssss", 
        $job['id'], 
        $job['title'], 
        $job['description'], 
        $job['qualifications'], 
        $job['location'], 
        $job['department'], 
        $job['pay'], 
        $job['job_type'], 
        $job['deadline'], 
        $job['status']
    );
    if ($stmt->execute()) {
        echo "Job '{$job['title']}' inserted successfully.<br>";
    } else {
        echo "Error inserting job '{$job['title']}': " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// Insert a default admin user
$admin_email = 'admin@starbucks.com';
$admin_password = password_hash('password', PASSWORD_DEFAULT);
$admin_role = 'admin';

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $insert_admin_sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_admin_sql);
    $stmt_insert->bind_param("sss", $admin_email, $admin_password, $admin_role);
    if ($stmt_insert->execute()) {
        echo "Default admin user created successfully.<br>";
    } else {
        echo "Error creating default admin user: " . $stmt_insert->error . "<br>";
    }
    $stmt_insert->close();
} else {
    echo "Admin user already exists.<br>";
}
$stmt->close();

$conn->close();

echo "<br>Setup completed successfully! You can now use the system.";
?> 