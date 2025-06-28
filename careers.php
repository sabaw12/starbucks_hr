<?php
$page_title = 'Careers at Starbucks';
include 'header.php';
include 'config.php';

// Fetch jobs from database
$sql = "SELECT * FROM jobs WHERE status = 'Open' ORDER BY id";
$result = $conn->query($sql);
$jobs = $result->fetch_all(MYSQLI_ASSOC);
?>
<style>
        .hero { position: relative; background-image: url('star1.jpg'); background-size: cover; background-position: center; color: #fff; padding: 80px 0 60px 0; text-align: center; height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .hero .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 1; }   
        .hero .container { position: relative; z-index: 2; }
</style>
<!-- Hero Section -->
<div class="hero">
    <div class="overlay"></div>
    <div class="container">
        <h1>Join Our Team</h1>
        <p class="lead">Discover career opportunities and become part of the Starbucks family.<br>We're looking for passionate individuals to help us create meaningful connections.</p>
        <form id="jobSearchForm" class="row justify-content-center search-bar g-2 mb-4" onsubmit="return false;">
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchPosition" placeholder="Search position title...">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" id="searchLocation" placeholder="Location...">
            </div>
            <div class="col-md-2">
                <button class="btn btn-success w-100" type="submit"><i class="bi bi-search"></i> Search</button>
            </div>
        </form>
    </div>
</div>

<!-- Main Content for Careers -->
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-3">Current Openings</h2>
            <p class="text-muted" id="job-count">Found <?php echo count($jobs); ?> positions</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="track_application.php" class="btn btn-outline-primary">
                <i class="bi bi-search"></i> Track Your Application
            </a>
        </div>
    </div>

    <div class="job-listings">
        <?php if (empty($jobs)): ?>
            <div class="alert alert-info">There are currently no open positions. Please check back later.</div>
        <?php else: ?>
            <?php foreach ($jobs as $job): ?>
            <div class="card job-card mb-3" data-title="<?php echo strtolower(htmlspecialchars($job['title'])); ?>" data-location="<?php echo strtolower(htmlspecialchars($job['location'])); ?>">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($job['title']); ?></h5>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($job['department']); ?></p>
                            <div class="job-meta d-flex text-muted small gap-3 mb-3">
                                <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                <span><i class="bi bi-currency-dollar"></i> <?php echo htmlspecialchars($job['pay']); ?></span>
                                <span><i class="bi bi-clock"></i> <?php echo htmlspecialchars($job['job_type']); ?></span>
                            </div>
                            <p class="job-description"><?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 150))); ?>...</p>
                            <div class="job-tags">
                                <?php 
                                $tags = explode(',', $job['qualifications']);
                                foreach($tags as $tag): 
                                    if(!empty(trim($tag))): ?>
                                        <span class="badge bg-light text-dark fw-normal"><?php echo htmlspecialchars(trim($tag)); ?></span>
                                <?php endif; endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end d-flex flex-column justify-content-between">
                            <span class="badge job-type-badge align-self-end"><?php echo htmlspecialchars($job['job_type']); ?></span>
                            <div class="mt-3">
                                <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-outline-secondary me-2">View Details</a>
                                <a href="job_details.php?id=<?php echo $job['id']; ?>#apply-form" class="btn btn-success">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('jobSearchForm');
    const positionInput = document.getElementById('searchPosition');
    const locationInput = document.getElementById('searchLocation');
    const jobCards = document.querySelectorAll('.job-card');
    const jobCountElement = document.getElementById('job-count');

    function filterJobs() {
        const positionQuery = positionInput.value.toLowerCase();
        const locationQuery = locationInput.value.toLowerCase();
        let visibleJobs = 0;

        jobCards.forEach(card => {
            const title = card.dataset.title;
            const location = card.dataset.location;
            const isVisible = title.includes(positionQuery) && location.includes(locationQuery);
            card.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                visibleJobs++;
            }
        });

        jobCountElement.textContent = `Found ${visibleJobs} positions`;
    }

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        filterJobs();
    });
    
    positionInput.addEventListener('keyup', filterJobs);
    locationInput.addEventListener('keyup', filterJobs);
});
</script> 