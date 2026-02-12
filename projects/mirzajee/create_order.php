<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: login.php");

// 1. Fetch History for Auto-Fill Suggestions
$history_names = $conn->query("SELECT DISTINCT customer_name FROM orders ORDER BY customer_name ASC");
$history_phones = $conn->query("SELECT DISTINCT phone FROM orders ORDER BY phone ASC");
$history_cnics = $conn->query("SELECT DISTINCT cnic FROM orders WHERE cnic != '' ORDER BY cnic ASC");

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = cleanInput($_POST['name']);
    $phone = cleanInput($_POST['phone']);
    $cnic = cleanInput($_POST['cnic']); // Now Required
    $service = cleanInput($_POST['service']);
    
    // Financials
    $fee = cleanInput($_POST['fee']); 
    $paid = cleanInput($_POST['paid']); 
    
    // 2. LOGIC CHANGE: Tracking ID is now the CNIC
    $tracking_id = $cnic;

    // 3. Duplicate Check: If this CNIC already has an order, append a counter (e.g. 34201...-2)
    $check_duplicate = $conn->query("SELECT count(*) as count FROM orders WHERE tracking_id LIKE '$tracking_id%'");
    $count = $check_duplicate->fetch_assoc()['count'];
    
    if ($count > 0) {
        // If 1 exists, new one becomes CNIC-2, etc.
        $tracking_id = $tracking_id . "-" . ($count + 1);
    }

    // Insert Order
    $stmt = $conn->prepare("INSERT INTO orders (tracking_id, customer_name, phone, cnic, service_id, total_fee, paid_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssidd", $tracking_id, $name, $phone, $cnic, $service, $fee, $paid);
    
    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        
        // Record Transaction
        if($paid > 0) {
            $desc = "Payment for Order #$tracking_id";
            $conn->query("INSERT INTO transactions (type, category, amount, description, order_id) VALUES ('Income', 'Order Payment', '$paid', '$desc', '$last_id')");
        }

        // Redirect to Receipt
        header("Location: receipt.php?id=$last_id");
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>New Application Entry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        function updatePrice() {
            var select = document.getElementById('serviceSelect');
            var price = select.options[select.selectedIndex].getAttribute('data-price');
            document.getElementById('feeInput').value = price;
        }
    </script>
</head>
<body class="bg-gray-100 font-sans flex text-gray-800">

    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 p-8 overflow-y-auto h-screen">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">New Application</h1>
        <p class="text-gray-500 mb-8">Create a new order. Tracking ID will be the Customer CNIC.</p>

        <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl border-t-4 border-[#0F3D3E]">
            
            <?php if(isset($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>

            <form method="POST" autocomplete="on">
                <h3 class="font-bold text-gray-700 border-b pb-2 mb-4 uppercase text-xs tracking-wider">Customer Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 text-sm font-bold mb-2">CNIC Number (Required)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="cnic" list="cnic_list" required 
                                   class="w-full border border-gray-300 p-3 pl-10 rounded focus:outline-none focus:border-green-600 bg-yellow-50" 
                                   placeholder="Enter CNIC (This will be the Receipt Number)">
                            <datalist id="cnic_list">
                                <?php while($row = $history_cnics->fetch_assoc()) { echo "<option value='".$row['cnic']."'>"; } ?>
                            </datalist>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">This will be used as the Tracking ID.</p>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-bold mb-2">Full Name</label>
                        <input type="text" name="name" list="name_list" required 
                               class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-green-600" 
                               placeholder="e.g. Ali Khan">
                        <datalist id="name_list">
                            <?php while($row = $history_names->fetch_assoc()) { echo "<option value='".$row['customer_name']."'>"; } ?>
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-gray-600 text-sm font-bold mb-2">Phone Number</label>
                        <input type="text" name="phone" list="phone_list" required 
                               class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-green-600" 
                               placeholder="0300-1234567">
                        <datalist id="phone_list">
                            <?php while($row = $history_phones->fetch_assoc()) { echo "<option value='".$row['phone']."'>"; } ?>
                        </datalist>
                    </div>
                </div>

                <h3 class="font-bold text-gray-700 border-b pb-2 mb-4 uppercase text-xs tracking-wider mt-8">Service & Payment</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 text-sm font-bold mb-2">Select Service</label>
                        <select name="service" id="serviceSelect" onchange="updatePrice()" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-green-600 bg-white">
                            <option value="" data-price="0">-- Select Service --</option>
                            <?php
                            $res = $conn->query("SELECT * FROM services ORDER BY name_en ASC");
                            while($r = $res->fetch_assoc()){
                                echo "<option value='".$r['id']."' data-price='".$r['price']."'>".$r['name_en']." (Rs. ".number_format($r['price']).")</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 text-sm font-bold mb-2">Total Fee</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">Rs.</span>
                            <input type="number" name="fee" id="feeInput" value="0" class="w-full border border-gray-300 p-3 pl-10 rounded focus:outline-none focus:border-green-600 font-bold text-gray-800">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 text-sm font-bold mb-2">Advance Payment</label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-500">Rs.</span>
                            <input type="number" name="paid" value="0" class="w-full border border-gray-300 p-3 pl-10 rounded focus:outline-none focus:border-green-600 font-bold text-green-700">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-4 mt-8 pt-4 border-t">
                    <a href="admin_orders.php" class="px-6 py-3 rounded text-gray-600 hover:bg-gray-100">Cancel</a>
                    <button type="submit" class="bg-[#0F3D3E] text-white px-8 py-3 rounded shadow hover:bg-green-900 transition font-bold flex items-center gap-2">
                        <i class="fas fa-save"></i> Save & Generate Receipt
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>