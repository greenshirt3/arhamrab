<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service | Mirza Ji Property</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/609/609803.png" type="image/png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; color: #e2e8f0; }
        h1, h2, h3 { font-family: 'Playfair Display', serif; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #0F172A; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
    </style>
</head>
<body class="antialiased relative">

    <header x-data="{ mobileMenu: false }" class="sticky top-0 z-40 bg-[#0F172A]/90 backdrop-blur-xl border-b border-white/5 transition-all">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="w-10 h-10 glass rounded-xl flex items-center justify-center text-[#D4AF37] text-xl">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white leading-none">MIRZA JI</h1>
                    <p class="text-[10px] text-gray-400 font-bold tracking-[0.2em] uppercase">Property Services</p>
                </div>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 font-semibold text-sm text-gray-300">
                <a href="index.php" class="hover:text-[#D4AF37] transition">Home</a>
                <a href="index.php#services" class="hover:text-[#D4AF37] transition">Services</a>
                <a href="index.php#track" class="hover:text-[#D4AF37] transition">Track Status</a>
            </nav>

             <button @click="mobileMenu = !mobileMenu" class="md:hidden text-2xl text-white focus:outline-none">
                <i :class="mobileMenu ? 'fas fa-times' : 'fas fa-bars'"></i>
            </button>
        </div>

        <div x-show="mobileMenu" class="md:hidden glass border-t border-white/10 absolute w-full left-0 p-6 flex flex-col gap-4">
            <a href="index.php" class="text-gray-200">Home</a>
            <a href="index.php#services" class="text-gray-200">Services</a>
        </div>
    </header>

    <section class="relative py-20 px-6 overflow-hidden">
        <div class="container mx-auto text-center relative z-10">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 text-[#D4AF37]">Terms of Service</h1>
            <p class="text-gray-400 max-w-2xl mx-auto">Please read carefully before using our services.</p>
        </div>
    </section>

    <section class="pb-24 px-6">
        <div class="container mx-auto max-w-4xl">
            <div class="glass p-8 md:p-12 rounded-3xl space-y-8 text-gray-300 leading-relaxed">
                
                <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-200 text-sm">
                    <strong>Important Disclaimer:</strong> Mirza Ji Property Services acts solely as a documentation facilitator and consultant. We are <strong>not</strong> a government entity. All final approvals regarding Land Registry, Mutations (Inteqal), and Tax returns lie with the relevant Government authorities (PLRA/FBR).
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">1. Scope of Services</h2>
                    <p>We provide consultancy and documentation assistance for:</p>
                    <ul class="list-disc pl-5 mt-3 space-y-2 text-gray-400">
                        <li>Drafting of legal deeds (Registry, Rent Agreement, Affidavits).</li>
                        <li>Online data entry for FBR and PLRA.</li>
                        <li>Obtaining Fard/Record copies from the Arazi Record Center (ARC).</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">2. Client Responsibilities</h2>
                    <p>The client agrees to provide:</p>
                    <ul class="list-disc pl-5 mt-3 space-y-2 text-gray-400">
                        <li>Accurate and authentic information/documents.</li>
                        <li>Original CNIC for biometric verification where required.</li>
                        <li>Correct property details (Khewat/Khasra No). We are not liable for errors caused by incorrect data provided by the client.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">3. Fees & Government Taxes</h2>
                    <p>Service charges quoted by Mirza Ji Property Services include our consultancy fee only. <strong>Government Taxes (CVT, Stamp Duty, Registration Fee, Withholding Tax)</strong> are separate and must be paid by the client via Bank Challan (PSID). We are not responsible for sudden changes in tax rates by the Govt of Pakistan.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">4. Process Timelines</h2>
                    <p>While we strive for the fastest turnaround, timelines for Registry approval and Mutation (Inteqal) depend on the availability of the Sub-Registrar or Tehsildar. We cannot guarantee specific completion dates if delays occur on the Government's end.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">5. Refunds & Cancellations</h2>
                    <p>Consultancy fees are non-refundable once the drafting or data entry work has commenced. If an application is rejected due to a fault in our drafting, we will re-process it free of cost. Rejections due to client ineligibility or document issues are not subject to refunds.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">6. Jurisdiction</h2>
                    <p>Any disputes arising from these terms shall be subject to the jurisdiction of the courts in <strong>Gujrat, Punjab</strong>.</p>
                </div>

            </div>
        </div>
    </section>

    <footer class="bg-[#050B14] text-gray-400 py-10 border-t border-white/10 text-center text-sm">
        <div class="container mx-auto px-6">
            <p>&copy; <?php echo date("Y"); ?> Mirza Ji Property Services. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>