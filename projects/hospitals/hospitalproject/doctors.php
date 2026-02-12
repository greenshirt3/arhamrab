<?php include 'header.php'; ?>

<div class="container-fluid py-5 bg-primary text-white mb-5" style="background: linear-gradient(45deg, var(--secondary), var(--primary));">
    <div class="container text-center py-5">
        <h1 class="display-4 fw-bold animated slideInDown">Meet Our Specialists</h1>
        <p class="lead mb-0">World-class experienced doctors ready to serve you.</p>
    </div>
</div>

<div class="container py-5">
    
    <div class="row mb-5 g-3 align-items-center">
        <div class="col-lg-8">
            <div class="d-flex flex-wrap gap-2" id="deptFilters">
                <button class="btn btn-dark active filter-btn" onclick="filterDoctors('all', this)">All</button>
                <?php foreach($data['departments'] as $dept): ?>
                    <button class="btn btn-outline-dark filter-btn" onclick="filterDoctors('<?php echo $dept['id']; ?>', this)">
                        <?php echo $dept['name']; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                <input type="text" id="docSearch" class="form-control border-start-0 ps-0" placeholder="Search doctor name..." onkeyup="searchDoctors()">
            </div>
        </div>
    </div>

    <div class="row g-4" id="doctorsGrid">
        <?php foreach($data['doctors'] as $doc): ?>
            <?php 
                $deptName = "Specialist"; 
                foreach($data['departments'] as $d) {
                    if($d['id'] == $doc['dept']) { $deptName = $d['name']; break; }
                }
            ?>
            
            <div class="col-md-6 col-lg-3 doc-item" data-dept="<?php echo $doc['dept']; ?>" data-name="<?php echo strtolower($doc['name']); ?>">
                <div class="doc-card h-100 position-relative">
                    <div class="position-relative">
                        <img src="<?php echo $doc['img']; ?>" class="w-100" style="height: 250px; object-fit: cover;" alt="<?php echo $doc['name']; ?>">
                        <div class="bg-primary text-white position-absolute bottom-0 start-0 px-3 py-1 m-2 rounded small">
                            <?php echo $deptName; ?>
                        </div>
                    </div>
                    <div class="p-4 text-center">
                        <h5 class="fw-bold text-secondary mb-1"><?php echo $doc['name']; ?></h5>
                        <p class="text-muted small mb-2"><?php echo $doc['qual']; ?></p>
                        <h5 class="text-primary fw-bold mb-3"><?php echo $info['currency'] . ' ' . number_format($doc['fee']); ?></h5>
                        
                        <div class="d-grid gap-2">
                            <a href="appointment.php?doc=<?php echo urlencode($doc['name']); ?>" class="btn btn-outline-primary btn-sm">Book Appointment</a>
                            <a href="https://wa.me/<?php echo str_replace(['+',' '], '', $info['phone']); ?>?text=I want to check OPD timings for <?php echo $doc['name']; ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="fab fa-whatsapp me-1"></i> Chat
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="noResults" class="text-center py-5 d-none">
        <i class="fa fa-user-md fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No doctors found matching your search.</h4>
    </div>

</div>

<script>
    function filterDoctors(category, btn) {
        // Update Buttons
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('btn-dark', 'active');
            b.classList.add('btn-outline-dark');
        });
        btn.classList.remove('btn-outline-dark');
        btn.classList.add('btn-dark', 'active');

        // Filter Logic
        const items = document.querySelectorAll('.doc-item');
        let visibleCount = 0;

        items.forEach(item => {
            if (category === 'all' || item.getAttribute('data-dept') === category) {
                item.style.display = 'block';
                item.classList.add('animate__animated', 'animate__fadeIn');
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        checkResults(visibleCount);
    }

    function searchDoctors() {
        const input = document.getElementById('docSearch').value.toLowerCase();
        const items = document.querySelectorAll('.doc-item');
        let visibleCount = 0;

        items.forEach(item => {
            const name = item.getAttribute('data-name');
            // If we are currently filtered by a category, we should respect that too
            // But for simplicity, search usually overrides filters or works within 'all'
            if (name.includes(input)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        checkResults(visibleCount);
    }

    function checkResults(count) {
        const msg = document.getElementById('noResults');
        if(count === 0) {
            msg.classList.remove('d-none');
        } else {
            msg.classList.add('d-none');
        }
    }
</script>

<?php include 'footer.php'; ?>