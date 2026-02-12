<?php require_once __DIR__ . '/../auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Radiology Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .upload-zone { border: 3px dashed #ccc; border-radius: 20px; padding: 50px; text-align: center; transition: 0.3s; background: #f9f9f9; }
        .upload-zone:hover { border-color: var(--primary); background: #e0f2fe; }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container py-5">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="fw-bold"><i class="fas fa-radiation me-2 text-danger"></i> Radiology & Imaging</h2>
            <a href="dashboard.php" class="btn btn-outline-light">Back to Lab</a>
        </div>

        <div class="glass-panel p-5 text-dark bg-white">
            <h4 class="mb-4">Upload X-Ray / MRI Scan</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Select Patient</label>
                    <input type="text" class="form-control smart-search" 
                           data-type="patient" placeholder="Type Name or ID..." autocomplete="off">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Modality</label>
                    <select class="form-select">
                        <option>X-Ray (Digital)</option>
                        <option>MRI</option>
                        <option>CT Scan</option>
                    </select>
                </div>
                <div class="col-md-4">
                     <label class="form-label fw-bold">Body Part</label>
                     <input type="text" class="form-control" placeholder="e.g. Chest PA">
                </div>
            </div>

            <div class="upload-zone mt-5" id="dropZone">
                <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                <h5>Drag & Drop DICOM/JPG files here</h5>
                <p class="text-muted">or click to browse</p>
                <input type="file" id="fileInput" hidden>
            </div>
            <div class="mt-4 text-end">
                <button class="btn btn-primary btn-lg px-5">Upload & Save to PACS</button>
            </div>
        </div>
    </div>
    <script src="../assets/smart-search.js"></script>
    <script>
        const zone = document.getElementById('dropZone');
        zone.addEventListener('click', () => document.getElementById('fileInput').click());
    </script>
</body>
</html>