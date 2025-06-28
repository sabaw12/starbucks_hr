<?php
$page_title = 'Contact HR Help Desk';
include 'header.php';
?>

<div class="container py-4">
    <h2 class="section-title mb-4">Contact HR Help Desk</h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h5>Inquiry Form</h5>
                <form id="contactForm" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required pattern="^[A-Za-z\s]+$">
                        <div class="invalid-feedback">Name is required and must contain letters only.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">Valid email is required.</div>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required maxlength="1000"></textarea>
                        <div class="invalid-feedback">Message is required (max 1000 characters).</div>
                    </div>
                    <button type="submit" class="btn btn-success">Send Inquiry</button>
                </form>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card p-4">
                <h5>HR Contact Information</h5>
                <ul class="list-unstyled">
                    <li><strong>Email:</strong> hr@starbucks.com</li>
                    <li><strong>Phone:</strong> 8330-8462</li>
                    <li><strong>Location:</strong> Pureza, PH</li>
                </ul>
            </div>
        </div>
    </div>
    <a href="index.php" class="btn btn-outline-dark mt-3"><i class="bi bi-arrow-left"></i> Back to Home</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    var form = document.getElementById('contactForm');
    if (!form) return;
    form.addEventListener('submit', function(e) {
    var valid = true;
    if (!form.name.value.trim() || !/^[A-Za-z\s]+$/.test(form.name.value.trim())) {
        form.name.classList.add('is-invalid');
        valid = false;
    } else {
        form.name.classList.remove('is-invalid');
    }
    if (!/^\S+@\S+\.\S+$/.test(form.email.value.trim())) {
        form.email.classList.add('is-invalid');
        valid = false;
    } else {
        form.email.classList.remove('is-invalid');
    }
    if (!form.message.value.trim() || form.message.value.length > 1000) {
        form.message.classList.add('is-invalid');
        valid = false;
    } else {
        form.message.classList.remove('is-invalid');
    }
    if (!valid) {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }
    });
})();
</script>
<script>
document.getElementById('name').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^A-Za-z\s]/g, '');
});
</script>
</body>
</html> 