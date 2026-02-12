<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | Mirza Ji Property</title>
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
            <h1 class="text-4xl md:text-6xl font-bold mb-4 text-[#D4AF37]">Privacy Policy</h1>
            <p class="text-gray-400 max-w-2xl mx-auto">Last Updated: January 2026</p>
        </div>
    </section>

    <section class="pb-24 px-6">
        <div class="container mx-auto max-w-4xl">
            <div class="glass p-8 md:p-12 rounded-3xl space-y-8 text-gray-300 leading-relaxed">
                
                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">1. Introduction</h2>
                    <p>Mirza Ji Property Services ("we", "our", or "us"), located in Jalalpur Jattan, Gujrat, is committed to protecting the privacy of our clients. This Privacy Policy explains how we collect, use, and safeguard your personal information in compliance with the laws of Pakistan, including the <strong>Prevention of Electronic Crimes Act (PECA) 2016</strong>.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">2. Information We Collect</h2>
                    <p>To process Property Registry, Fard issuance, and FBR filings, we are required to collect the following:</p>
                    <ul class="list-disc pl-5 mt-3 space-y-2 text-gray-400">
                        <li><strong>Personal Identity:</strong> CNIC copies, Biometric verification status, and Passport details.</li>
                        <li><strong>Property Details:</strong> Khewat/Khatooni numbers, Khasra numbers, and Property Deeds (Fard).</li>
                        <li><strong>Financial Data:</strong> Tax payment receipts (PSID), bank challans, and FBR NTN details.</li>
                    </ul>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">3. Use of Information</h2>
                    <p>Your data is used strictly for:</p>
                    <ul class="list-disc pl-5 mt-3 space-y-2 text-gray-400">
                        <li>Submission to <strong>Punjab Land Records Authority (PLRA)</strong> for registry and mutation.</li>
                        <li>Filing tax returns via the <strong>Federal Board of Revenue (FBR)</strong> Iris portal.</li>
                        <li>Verification with NADRA e-Sahulat (for biometric purposes only).</li>
                    </ul>
                    <p class="mt-4 border-l-4 border-[#D4AF37] pl-4 italic text-[#D4AF37]">We do not sell, rent, or trade your personal data to third-party marketing agencies.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">4. Data Retention</h2>
                    <p>Hard copies of documents (Registry files) are returned to the client upon completion. Digital records of your Tracking ID and transaction history are stored securely in our encrypted database for record-keeping and audit purposes for up to 5 years, as recommended by FBR record-keeping guidelines.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">5. Security</h2>
                    <p>We implement strict security measures to protect your digital data. Access to our admin panel is restricted to authorized staff only. However, please note that no method of transmission over the internet is 100% secure.</p>
                </div>

                <div>
                    <h2 class="text-2xl text-white font-bold mb-4">6. Contact Us</h2>
                    <p>If you have questions about your data, please visit our office at Benazir Chowk, Jalalpur Jattan, or contact us at <strong>0300-7329510</strong>.</p>
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