<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: login.php");

// Quick Stats
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status != 'Completed'")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT SUM(total_fee) as s FROM orders")->fetch_assoc()['s'];
$today_income = $conn->query("SELECT SUM(amount) as s FROM transactions WHERE type='Income' AND DATE(date) = CURDATE()")->fetch_assoc()['s'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 flex flex-col md:flex-row font-sans min-h-screen">

    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 p-8 overflow-x-hidden">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Business Overview</h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm">Total Applications</p>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo $total_orders; ?></h2>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
                <p class="text-gray-500 text-sm">Pending Work</p>
                <h2 class="text-2xl font-bold text-gray-800"><?php echo $pending; ?></h2>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <h2 class="text-2xl font-bold text-green-700">Rs. <?php echo number_format($revenue); ?></h2>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-purple-500">
                <p class="text-gray-500 text-sm">Cash Collected Today</p>
                <h2 class="text-2xl font-bold text-purple-700">Rs. <?php echo number_format($today_income); ?></h2>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 font-bold text-gray-700">Recent Applications</div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm">
                            <th class="p-4">ID</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Service</th>
                            <th class="p-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT orders.*, services.name_en FROM orders JOIN services ON orders.service_id = services.id ORDER BY id DESC LIMIT 5");
                        while($row = $res->fetch_assoc()){
                            echo "<tr class='border-b hover:bg-gray-50'>";
                            echo "<td class='p-4 text-sm font-bold'>".$row['tracking_id']."</td>";
                            echo "<td class='p-4 text-sm'>".$row['customer_name']."</td>";
                            echo "<td class='p-4 text-sm'>".$row['name_en']."</td>";
                            echo "<td class='p-4'><span class='text-xs px-2 py-1 rounded bg-gray-200'>".$row['status']."</span></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 text-center">
                <a href="admin_orders.php" class="text-blue-600 hover:underline text-sm">View All Orders</a>
            </div>
        </div>
    </div>

</body>
</html>