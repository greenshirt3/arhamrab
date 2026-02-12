<div class="md:hidden bg-[#0F3D3E] text-white p-4 flex justify-between items-center shadow-md">
    <span class="font-bold text-lg">MIRZA JI ADMIN</span>
    <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="text-white text-2xl focus:outline-none">
        <i class="fas fa-bars"></i>
    </button>
</div>

<div id="sidebar" class="bg-[#0F3D3E] text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out z-50 min-h-screen flex flex-col shadow-xl">
    <div class="text-center font-bold text-xl border-b border-green-800 pb-4 px-4">
        <i class="fas fa-building text-yellow-500 mb-2"></i><br>
        ADMIN PORTAL
    </div>

    <nav class="flex-grow p-2 space-y-2">
        <a href="admin_dashboard.php" class="block py-2.5 px-4 rounded hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF'])=='admin_dashboard.php'?'bg-green-900 border-l-4 border-yellow-500':'' ?>">
            <i class="fas fa-chart-line w-6 mr-2"></i> Dashboard
        </a>
        <a href="admin_orders.php" class="block py-2.5 px-4 rounded hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF'])=='admin_orders.php'?'bg-green-900 border-l-4 border-yellow-500':'' ?>">
            <i class="fas fa-folder-open w-6 mr-2"></i> Orders
        </a>
        <a href="admin_services.php" class="block py-2.5 px-4 rounded hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF'])=='admin_services.php'?'bg-green-900 border-l-4 border-yellow-500':'' ?>">
            <i class="fas fa-list w-6 mr-2"></i> Services
        </a>
        <a href="admin_finance.php" class="block py-2.5 px-4 rounded hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF'])=='admin_finance.php'?'bg-green-900 border-l-4 border-yellow-500':'' ?>">
            <i class="fas fa-wallet w-6 mr-2"></i> Finance
        </a>
    </nav>
    
    <div class="p-4 border-t border-green-800">
        <a href="logout.php" class="block py-2 px-4 text-red-300 hover:text-white">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>
</div>

<div onclick="document.getElementById('sidebar').classList.add('-translate-x-full')" class="md:hidden fixed inset-0 bg-black opacity-50 z-40 hidden" id="sidebar-overlay"></div>