<?php
session_start();
include 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: admin_dashboard_enhanced.php');
    } else {
        header('Location: employee_portal.php');
    }
    exit();
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $login_error = 'Please fill in all fields.';
    } else {
        // First check hardcoded demo accounts for backward compatibility
        $demo_accounts = [
            'admin@starbucks.com' => ['password' => 'rodjecyrus', 'role' => 'admin'],
            'jazper@starbucks.com' => ['password' => 'jazperga', 'role' => 'employee'],
            'manager@starbucks.com' => ['password' => 'alexandra', 'role' => 'manager'],
        ];
        
        // Check demo accounts first
        if (isset($demo_accounts[$email]) && $demo_accounts[$email]['password'] === $password) {
            $_SESSION['user'] = [
                'id' => 1, // Demo ID
                'email' => $email,
                'role' => $demo_accounts[$email]['role']
            ];
            
            if ($demo_accounts[$email]['role'] === 'admin') {
                header('Location: admin_dashboard_enhanced.php');
            } else {
                header('Location: employee_portal.php');
            }
            exit();
        } else {
            // Check database users
            $stmt = $conn->prepare("SELECT id, email, password, role FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    if ($user['role'] === 'admin') {
                        header('Location: admin_dashboard_enhanced.php');
                    } else {
                        header('Location: employee_portal.php');
                    }
                    exit();
                } else {
                    $login_error = 'Invalid email or password.';
                }
            } else {
                $login_error = 'Invalid email or password.';
            }
            $stmt->close();
        }
    }
}

$page_title = 'Employee Portal Login';
include 'header.php';
?>

<div class="container">
    <div class="login-header">
        <span style="display:inline-block;width:80px;height:80px;margin-bottom:10px;">
        <img src="starbslogo.png" alt="Starbucks Logo" class="img-fluid" style="width:100%;height:100%;object-fit:contain;">
        </span>
        <h2>Employee Portal</h2>
        <p>Sign in to access your HR dashboard</p>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="fw-bold mb-2">Sign In</h4>
                    <p class="mb-4 text-muted">Enter your credentials to access the employee portal</p>
                    <form method="post" action="login.php" id="loginForm" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required minlength="6">
                                <span class="input-group-text" style="cursor:pointer;" onclick="togglePassword()"><i class="bi bi-eye"></i></span>
                                <div class="invalid-feedback">Password must be at least 6 characters.</div>
                            </div>
                        </div>
                        <?php if ($login_error): ?>
                            <div class="alert alert-danger py-2" role="alert"><?php echo htmlspecialchars($login_error); ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-success w-100 mb-2">Sign In</button>
                    </form>           
                    
                    <a href="index.php" class="back-link"><i class="bi bi-arrow-left"></i> Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.input-group-text i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
}

// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    let isValid = true;
    
    if (!email.value || !email.checkValidity()) {
        email.classList.add('is-invalid');
        isValid = false;
    } else {
        email.classList.remove('is-invalid');
    }
    
    if (!password.value || password.value.length < 6) {
        password.classList.add('is-invalid');
        isValid = false;
    } else {
        password.classList.remove('is-invalid');
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>
