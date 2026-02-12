<?php 
include 'config.php'; 

// Helper for Service Icons
function getServiceIcon($name) {
    if(stripos($name, 'Registry') !== false) return 'fa-file-signature';
    if(stripos($name, 'Fard') !== false) return 'fa-scroll';
    if(stripos($name, 'FBR') !== false) return 'fa-calculator';
    if(stripos($name, 'Power') !== false) return 'fa-gavel';
    if(stripos($name, 'Rent') !== false) return 'fa-house-user';
    if(stripos($name, 'Deed') !== false) return 'fa-hand-holding-heart';
    if(stripos($name, 'Vehicle') !== false || stripos($name, 'Driving') !== false) return 'fa-car';
    return 'fa-file-contract';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mirza Ji Property | World-Class Documentation</title>
    <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/609/609803.png" type="image/png">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <style>
        /* --- Typography Setup --- */
        @font-face { 
            font-family: 'JameelNoori'; 
            src: url('font/jameelnoorinastaleeq.ttf') format('truetype'); 
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        /* Background matches the Business Card's Deep Green/Navy Vibe */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #022c22; color: #fff; overflow-x: hidden; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
        
        /* Updated: Only uses Jameel Noori now */
        .font-urdu { font-family: 'JameelNoori', serif; line-height: 1.8; }

        /* Color Variables from Business Card */
        :root {
            --brand-green: #006837; 
            --brand-navy: #003366; 
            --brand-gold: #eab308;
        }

        /* Glassmorphism Utilities */
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Premium Card Style */
        .glass-card {
            background: linear-gradient(145deg, rgba(0, 104, 55, 0.4), rgba(0, 51, 102, 0.4));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        /* Background Gradient Mesh */
        .mesh-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background: radial-gradient(circle at 10% 20%, #004d26 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, #003366 0%, transparent 40%),
                        radial-gradient(circle at 50% 50%, #022c22 100%);
        }

        /* 3D Tilt Effect */
        .card-3d-wrapper { perspective: 1000px; }
        .card-3d {
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            transform-style: preserve-3d;
        }
        .card-3d:hover {
            transform: translateY(-10px) rotateX(5deg);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border-top: 1px solid var(--brand-gold);
        }
        .card-content { transform: translateZ(30px); }

        /* Floating Owner Image Animation */
        .owner-float { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #022c22; }
        ::-webkit-scrollbar-thumb { background: #006837; border-radius: 4px; }
    </style>
</head>
<body class="antialiased relative selection:bg-green-500 selection:text-white">

    <div class="mesh-bg"></div>

    <div class="relative z-50 bg-[#003366] text-white py-2 text-xs md:text-sm border-b border-white/10 shadow-lg">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <span class="flex items-center gap-2"><i class="fas fa-map-marker-alt text-yellow-400"></i> Opposite Faysal Bank, Jalalpur Jattan</span>
            </div>
            <a href="login.php" class="flex items-center gap-2 hover:text-yellow-400 transition">
                <i class="fas fa-lock"></i> <span>Admin Login</span>
            </a>
        </div>
    </div>

    <header x-data="{ mobileMenu: false, scrolled: false }" 
            @scroll.window="scrolled = (window.pageYOffset > 20)"
            :class="{ 'bg-[#022c22]/90 backdrop-blur-xl shadow-2xl': scrolled, 'bg-transparent': !scrolled }"
            class="sticky top-0 z-40 transition-all duration-300 border-b border-white/5">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            
            <a href="index.php" class="flex items-center gap-3 group">
                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center text-[#006837] text-2xl shadow-lg border-2 border-[#006837]">
                    <i class="fas fa-building"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white leading-none">MIRZA JI</h1>
                    <p class="text-[10px] text-yellow-400 font-bold tracking-[0.3em] uppercase">Property Services</p>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-200">
                <a href="#" class="hover:text-yellow-400 transition">Home</a>
                <a href="#services" class="hover:text-yellow-400 transition">Services</a>
                <a href="#track" class="hover:text-yellow-400 transition">Track Status</a>
                <a href="https://wa.me/923114101053" class="bg-[#006837] border border-green-500 text-white px-6 py-2.5 rounded-full shadow-lg hover:bg-white hover:text-[#006837] transition transform hover:-translate-y-0.5">
                    <i class="fab fa-whatsapp mr-1"></i> Contact Owner
                </a>
            </nav>

            <button @click="mobileMenu = !mobileMenu" class="md:hidden text-2xl text-white">
                <i :class="mobileMenu ? 'fas fa-times' : 'fas fa-bars'"></i>
            </button>
        </div>

        <div x-show="mobileMenu" class="md:hidden bg-[#003366] border-t border-white/10 absolute w-full left-0 shadow-2xl p-6 flex flex-col gap-4">
            <a href="#" class="text-gray-200 hover:text-yellow-400">Home</a>
            <a href="#services" class="text-gray-200 hover:text-yellow-400">Services</a>
            <a href="#track" class="text-gray-200 hover:text-yellow-400">Track Status</a>
            <a href="https://wa.me/923114101053" class="bg-yellow-400 text-black text-center py-3 rounded-lg font-bold">WhatsApp Now</a>
        </div>
    </header>

    <section class="relative pt-12 pb-24 lg:py-32 px-6 overflow-hidden">
        <div class="container mx-auto relative z-10">
            <div class="flex flex-col-reverse lg:flex-row items-center gap-12 lg:gap-20">
                
                <div class="lg:w-1/2 text-center lg:text-left">
                    
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border-green-500/30 mb-6 animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-xs font-bold tracking-widest uppercase text-green-300">Punjab Land Record Authority Approved</span>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight drop-shadow-xl font-urdu">
                        <span class="text-white">مرزا جی</span><br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">پراپرٹی سروسز</span>
                    </h1>
                    
                    <h2 class="text-2xl font-bold text-gray-300 mb-8">Abdul Aziz Mirza</h2>
                    
                    <p class="text-gray-400 text-lg mb-10 max-w-lg mx-auto lg:mx-0">
                        Expert documentation services in Jalalpur Jattan. We handle Registry, Fard, FBR Filing, and Legal Deeds with 100% security.
                    </p>

                    <div id="track" class="glass-card p-2 rounded-full shadow-2xl flex items-center relative overflow-hidden group max-w-md mx-auto lg:mx-0">
                        <form method="GET" class="flex w-full relative z-10 gap-2">
                            <input type="text" name="track" placeholder="Enter CNIC (e.g. 34201...)" 
                                   class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-gray-400 px-6 py-4 text-lg"
                                   value="<?php echo isset($_GET['track']) ? $_GET['track'] : ''; ?>" required>
                            <button type="submit" class="bg-[#006837] text-white px-8 py-4 rounded-full font-bold hover:bg-yellow-400 hover:text-black transition shadow-lg">
                                Track
                            </button>
                        </form>
                    </div>

                    <?php if (isset($_GET['track'])): ?>
                        <div class="mt-8 bg-white/10 backdrop-blur-md rounded-xl p-6 border-l-4 border-yellow-400 text-left animate-fade-in-up">
                            <?php
                            $track_id = cleanInput($_GET['track']);
                            $sql = "SELECT orders.*, services.name_en FROM orders JOIN services ON orders.service_id = services.id WHERE tracking_id = '$track_id'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                echo "<h3 class='text-xl font-bold text-white mb-2'><i class='fas fa-check-circle text-green-400'></i> Found: ".$row['customer_name']."</h3>";
                                echo "<p class='text-gray-300'>Status: <span class='text-yellow-400 font-bold'>".$row['status']."</span></p>";
                                echo "<p class='text-gray-400 text-sm mt-2'>".$row['remarks']."</p>";
                            } else {
                                echo "<p class='text-red-300'><i class='fas fa-times-circle'></i> Record not found.</p>";
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="lg:w-1/2 relative owner-float">
                    <div class="relative w-80 h-80 md:w-96 md:h-96 mx-auto">
                        <div class="absolute inset-0 bg-gradient-to-tr from-[#006837] to-[#003366] rounded-full blur-2xl opacity-60"></div>
                        
                        <div class="relative w-full h-full rounded-full border-4 border-white/10 overflow-hidden shadow-2xl glass-card flex items-end justify-center bg-gradient-to-b from-transparent to-[#003366]/80">
                            <img src="owner.png" alt="Abdul Aziz Mirza" class="w-full h-full object-cover object-top hover:scale-105 transition duration-500"
                                 onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png';"> 
                        </div>

                        <div class="absolute bottom-4 -right-4 bg-white text-[#003366] px-6 py-3 rounded-xl shadow-xl font-bold flex items-center gap-3 animate-bounce">
                            <div class="bg-green-100 p-2 rounded-full"><i class="fas fa-certificate text-green-600"></i></div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase">Experience</p>
                                <p class="text-lg leading-none">15+ Years</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="services" class="py-24 relative z-10">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <span class="text-yellow-400 font-bold tracking-[0.2em] text-xs uppercase mb-4 block">What We Do</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Our Services</h2>
                <div class="w-24 h-1 bg-[#006837] mx-auto rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $sql = "SELECT * FROM services ORDER BY id ASC";
                $result = $conn->query($sql);
                while($row = $result->fetch_assoc()) {
                    $icon = getServiceIcon($row['name_en']);
                ?>
                <div class="card-3d-wrapper h-full">
                    <div class="glass-card p-8 rounded-2xl h-full card-3d relative group overflow-hidden border-t-4 border-[#006837]">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#006837] to-[#003366] opacity-0 group-hover:opacity-100 transition duration-500 -z-10"></div>
                        
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center text-white text-2xl border border-white/20 shadow-inner group-hover:bg-white group-hover:text-[#006837] transition">
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <span class="text-xs font-mono text-gray-400 group-hover:text-white/80">#<?php echo $row['id']; ?></span>
                        </div>
                        
                        <div class="card-content">
                            <h3 class="text-xl font-bold text-white mb-1"><?php echo $row['name_en']; ?></h3>
                            <h4 class="text-2xl font-urdu text-yellow-400 mb-4"><?php echo $row['name_ur']; ?></h4>
                            
                            <div class="h-px w-full bg-white/10 my-4"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider group-hover:text-white">Fee Starts</span>
                                <span class="text-xl font-bold text-white">Rs. <?php echo number_format($row['price']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <footer class="bg-[#0f172a] text-gray-400 pt-24 pb-12 border-t border-[#006837] relative overflow-hidden">
        
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-[#006837] opacity-10 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-[#006837] shadow-lg">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white text-xl font-bold">MIRZA JI</h3>
                            <p class="text-[10px] text-yellow-500 font-bold uppercase mt-1">Property Services</p>
                        </div>
                    </div>
                    <p class="leading-relaxed mb-8 text-sm text-gray-500">
                        Secure Property Registry, Fard issuance, and FBR Tax filing in Jalalpur Jattan.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full glass border border-white/10 flex items-center justify-center hover:bg-[#1877F2] hover:text-white hover:border-transparent transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/923114101053" class="w-10 h-10 rounded-full glass border border-white/10 flex items-center justify-center hover:bg-[#25D366] hover:text-white hover:border-transparent transition"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg mb-8 relative inline-block">
                        Quick Links
                        <span class="absolute -bottom-2 left-0 w-12 h-1 bg-[#006837] rounded-full"></span>
                    </h4>
                    <ul class="space-y-4 text-sm">
                        <li><a href="index.php" class="hover:text-yellow-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-[#006837]"></i> Home</a></li>
                        <li><a href="index.php#services" class="hover:text-yellow-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-[#006837]"></i> Services</a></li>
                        <li><a href="login.php" class="hover:text-yellow-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-[#006837]"></i> Staff Login</a></li>
                        <li><a href="privacy.php" class="hover:text-yellow-400 transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs text-[#006837]"></i> Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold text-lg mb-8 relative inline-block">
                        Contact
                        <span class="absolute -bottom-2 left-0 w-12 h-1 bg-[#006837] rounded-full"></span>
                    </h4>
                    <ul class="space-y-6">
                        <li>
                            <a href="https://www.google.com/maps/search/?api=1&query=Opposite+Faysal+Bank,+Benazir+Chowk,+Jalalpur+Jattan" target="_blank" class="flex items-start gap-4 group hover:bg-white/5 p-3 rounded-xl transition -mx-3">
                                <div class="text-yellow-500"><i class="fas fa-map-marker-alt text-xl"></i></div>
                                <div>
                                    <span class="block text-white text-sm font-bold">Visit Office</span>
                                    <span class="text-xs text-gray-400">Opposite Faysal Bank, Benazir Chowk, Jalalpur Jattan</span>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="tel:+923114101053" class="flex items-center gap-4 group hover:bg-white/5 p-3 rounded-xl transition -mx-3">
                                <div class="text-yellow-500"><i class="fas fa-phone-alt text-xl"></i></div>
                                <div>
                                    <span class="block text-white text-sm font-bold">Call Us</span>
                                    <span class="text-xs text-gray-400">0300-7329510</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <div class="glass p-6 rounded-2xl border border-white/5 relative group hover:border-yellow-400/30 transition-all duration-300">
                        <div class="absolute -top-3 -right-3 bg-[#0f172a] p-2 rounded-full border border-white/10 group-hover:border-yellow-400 transition">
                            <i class="fas fa-code text-yellow-400"></i>
                        </div>
                        <h4 class="text-white font-bold mb-1">Tech Partner</h4>
                        <p class="text-[10px] text-yellow-400 uppercase tracking-widest mb-4">Powered by Arham Printers</p>
                        <p class="text-xs leading-relaxed mb-6 text-gray-500">
                            Digital portal built by Arham Printers Tech Division.
                        </p>
                        <a href="https://arhamprinters.pk" target="_blank" class="w-full block text-center bg-white/5 hover:bg-yellow-400 hover:text-black text-white text-xs font-bold py-3 rounded-lg border border-white/10 transition-all">
                            Visit arhamprinters.pk
                        </a>
                    </div>
                </div>

            </div>
            
            <div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500 gap-4">
                <p>© <?php echo date("Y"); ?> Mirza Ji Property Services. All Rights Reserved.</p>
                <div class="flex gap-6">
                    <a href="terms.php" class="hover:text-yellow-400 transition">Terms</a>
                    <a href="privacy.php" class="hover:text-yellow-400 transition">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    <a href="https://wa.me/923114101053" class="fixed bottom-6 right-6 bg-[#25D366] text-white w-14 h-14 rounded-full flex items-center justify-center shadow-[0_0_20px_rgba(37,211,102,0.4)] hover:bg-[#128C7E] hover:scale-110 transition duration-300 z-50 animate-bounce">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>

</body>
</html>