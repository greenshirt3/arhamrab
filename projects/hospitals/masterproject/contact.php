<?php 
include 'includes/header.php'; 

// Simple Logic to Handle Complaint Submission
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complaint = [
        'id' => generateID('CMP'),
        'name' => $_POST['name'],
        'message' => $_POST['message'],
        'date' => date('Y-m-d H:i:s'),
        'status' => 'New'
    ];
    // In a real scenario, append to complaints.json
    setFlash('success', "Complaint Registered! Your Tracking ID is " . $complaint['id']);
}
?>

<div class="container py-5">
    <?php displayFlash(); ?>
    
    <div class="row g-5">
        <div class="col-lg-5">
            <h1 class="fw-bold mb-4">Get in Touch</h1>
            <p class="text-secondary mb-5">Have a question or want to file a complaint? Our team is here to help you 24/7.</p>
            
            <div class="d-flex mb-4">
                <div class="me-3"><i class="fas fa-map-marker-alt fa-2x text-primary"></i></div>
                <div>
                    <h5 class="fw-bold">Hospital Location</h5>
                    <p class="text-muted"><?php echo HOSPITAL_ADDRESS; ?></p>
                </div>
            </div>
            
            <div class="d-flex mb-4">
                <div class="me-3"><i class="fas fa-phone-alt fa-2x text-primary"></i></div>
                <div>
                    <h5 class="fw-bold">Call Us</h5>
                    <p class="text-muted"><?php echo HOSPITAL_PHONE; ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="glass-panel p-5 bg-white">
                <h3 class="fw-bold mb-4">Complaint / Inquiry Form</h3>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Your Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Message / Complaint</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill">Submit Request</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>