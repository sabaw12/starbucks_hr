<?php
$page_title = 'Help Desk';
include 'header.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4">HR Help Desk</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h5>Frequently Asked Questions</h5>
                <ul>
                    <li><strong>How do I request leave?</strong><br>Login to the Employee Portal and use the Leave Request form.</li>
                    <li><strong>Where can I download HR forms?</strong><br>Visit the HR Documents page for downloadable forms and policies.</li>
                    <li><strong>How do I update my profile?</strong><br>Go to the Employee Portal and select Update Profile.</li>
                    <li><strong>Who do I contact for payroll issues?</strong><br>Email hr@starbucks.com or use the form below.</li>
                </ul>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h5>Contact HR Support</h5>
                <form id="helpDeskForm" novalidate>
                    <div class="mb-3">
                        <label for="helpName" class="form-label">Name</label>
                        <input type="text" class="form-control" id="helpName" name="name" required pattern="^[A-Za-z\s]+$">
                        <div class="invalid-feedback">Name is required and must contain letters only.</div>
                    </div>
                    <div class="mb-3">
                        <label for="helpEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="helpEmail" name="email" required>
                        <div class="invalid-feedback">Valid email is required.</div>
                    </div>
                    <div class="mb-3">
                        <label for="helpMessage" class="form-label">Message</label>
                        <textarea class="form-control" id="helpMessage" name="message" rows="4" required maxlength="1000"></textarea>
                        <div class="invalid-feedback">Message is required (max 1000 characters).</div>
                    </div>
                    <button type="submit" class="btn btn-success">Send Inquiry</button>
                </form>
            </div>
        </div>
    </div>
    <a href="index.php" class="btn btn-outline-dark mt-3"><i class="bi bi-arrow-left"></i> Back to Home</a>
</div>

<?php include 'footer.php'; ?>

<script>
document.getElementById('helpDeskForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Thank you for contacting HR! (Demo)');
});
</script> 