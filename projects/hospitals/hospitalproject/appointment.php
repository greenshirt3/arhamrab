<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row g-5">
        <div class="col-lg-4">
            <div class="bg-primary text-white p-4 rounded-3 mb-4">
                <h3><i class="fa fa-phone-alt me-2"></i> Call Center</h3>
                <p>Prefer to book via phone?</p>
                <h2 class="fw-bold"><?php echo $info['phone']; ?></h2>
            </div>
            <div class="bg-white p-4 rounded-3 shadow-sm border">
                <h5>Opening Hours</h5>
                <ul class="list-unstyled mt-3">
                    <li class="d-flex justify-content-between mb-2"><span>Mon - Sat</span> <span>09:00 - 21:00</span></li>
                    <li class="d-flex justify-content-between text-danger"><span>Sunday</span> <span>Closed</span></li>
                </ul>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="mb-4 text-primary fw-bold">Book an Appointment</h2>
                    <form onsubmit="bookOnWhatsApp(event)">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Patient Name</label>
                                <input type="text" id="pName" class="form-control bg-light" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" id="pPhone" class="form-control bg-light" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select id="deptSelect" class="form-select bg-light" onchange="filterDoctors()">
                                    <option value="">Select Department</option>
                                    <?php foreach($data['departments'] as $d) echo "<option value='{$d['id']}'>{$d['name']}</option>"; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Select Doctor</label>
                                <select id="docSelect" class="form-select bg-light">
                                    <option value="">Select Department First</option>
                                    </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Preferred Date</label>
                                <input type="date" id="pDate" class="form-control bg-light" required>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <button class="btn btn-dark w-100 py-3 fw-bold">Confirm & Open WhatsApp <i class="fab fa-whatsapp ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load Doctors from PHP into JS object
    const doctors = <?php echo json_encode($data['doctors']); ?>;

    function filterDoctors() {
        const dept = document.getElementById('deptSelect').value;
        const docSelect = document.getElementById('docSelect');
        docSelect.innerHTML = '<option value="">Select Doctor</option>';
        
        const filtered = doctors.filter(d => d.dept === dept);
        filtered.forEach(d => {
            docSelect.innerHTML += `<option value="${d.name}">${d.name} (Fee: ${d.fee})</option>`;
        });
    }

    function bookOnWhatsApp(e) {
        e.preventDefault();
        const name = document.getElementById('pName').value;
        const doc = document.getElementById('docSelect').value;
        const date = document.getElementById('pDate').value;
        
        if(!doc) { alert('Please select a doctor'); return; }

        const text = `*New Appointment Request*%0aName: ${name}%0aDoctor: ${doc}%0aDate: ${date}`;
        window.open(`https://wa.me/<?php echo str_replace(['+',' '], '', $info['phone']); ?>?text=${text}`, '_blank');
    }
</script>

<?php include 'footer.php'; ?>