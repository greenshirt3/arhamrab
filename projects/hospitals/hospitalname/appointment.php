<?php include 'header.php'; ?>

<div class="container-fluid py-5" style="background: linear-gradient(rgba(29, 42, 77, 0.9), rgba(29, 42, 77, 0.9)), url('<?php echo $hospital['hero_section']['image']; ?>') center center no-repeat; background-size: cover;">
    <div class="container">
        <div class="row gx-5">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="mb-4">
                    <h5 class="d-inline-block text-white text-uppercase border-bottom border-5">Appointment</h5>
                    <h1 class="display-4 text-white">Book via WhatsApp</h1>
                </div>
                <p class="text-white mb-5">Fill the details below and we will confirm your appointment instantly on WhatsApp.</p>
                <div class="bg-primary p-4 rounded">
                    <h4 class="text-white mb-0">
                        <a href="tel:<?php echo $info['phone']; ?>" class="text-white text-decoration-none">
                            <i class="fa fa-phone-alt me-2"></i>Call Us: <?php echo $info['phone']; ?>
                        </a>
                    </h4>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-white text-center rounded p-5">
                    <h1 class="mb-4">Appointment Details</h1>
                    <form onsubmit="sendToWhatsApp(event)">
                        <div class="row g-3">
                            <div class="col-12">
                                <input type="text" id="ptName" class="form-control bg-light border-0" placeholder="Patient Name" style="height: 55px;" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <select id="ptDoc" class="form-select bg-light border-0" style="height: 55px;">
                                    <option selected value="Any Doctor">Select Doctor (Optional)</option>
                                    <?php foreach($hospital['doctors'] as $doc): ?>
                                        <option value="<?php echo $doc['name']; ?>"><?php echo $doc['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6">
                                <input type="date" id="ptDate" class="form-control bg-light border-0" style="height: 55px;" required>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3" type="submit">Confirm on WhatsApp <i class="fab fa-whatsapp ms-2"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function sendToWhatsApp(e) {
        e.preventDefault();
        
        const name = document.getElementById('ptName').value;
        const doctor = document.getElementById('ptDoc').value;
        const date = document.getElementById('ptDate').value;
        const hospitalNumber = "<?php echo $info['whatsapp_clean']; ?>";

        let msg = `*New Appointment Request* %0a`;
        msg += `--------------------------- %0a`;
        msg += `Patient Name: ${name} %0a`;
        msg += `Doctor: ${doctor} %0a`;
        msg += `Preferred Date: ${date} %0a`;
        msg += `--------------------------- %0a`;
        msg += `Please confirm my slot.`;

        const url = `https://wa.me/${hospitalNumber}?text=${msg}`;
        window.open(url, '_blank');
    }
</script>

<?php include 'footer.php'; ?>