<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $id = cleanInput($_POST['order_id']);
    $status = cleanInput($_POST['status']);
    $remarks = cleanInput($_POST['remarks']);
    $stmt = $conn->prepare("UPDATE orders SET status=?, remarks=? WHERE id=?");
    $stmt->bind_param("ssi", $status, $remarks, $id);
    $stmt->execute();
    header("Location: admin_orders.php?msg=updated");
    exit();
}

if (isset($_GET['delete'])) {
    $id = cleanInput($_GET['delete']);
    $conn->query("DELETE FROM orders WHERE id=$id");
    header("Location: admin_orders.php?msg=deleted");
    exit();
}

$search_query = "";
$sql = "SELECT orders.*, services.name_en FROM orders JOIN services ON orders.service_id = services.id ";

// 4. IMPROVED SEARCH: Checks CNIC, Service, Name, Phone, and ID
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = cleanInput($_GET['q']);
    $search_query = $q;
    $sql .= "WHERE (
                orders.customer_name LIKE '%$q%' OR 
                orders.phone LIKE '%$q%' OR 
                orders.tracking_id LIKE '%$q%' OR 
                orders.cnic LIKE '%$q%' OR 
                orders.status LIKE '%$q%' OR 
                services.name_en LIKE '%$q%'
            ) ";
}
$sql .= "ORDER BY orders.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Orders | Mirza Ji Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans flex flex-col md:flex-row text-gray-800 min-h-screen">

    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <header class="bg-white shadow px-8 py-4 flex flex-col md:flex-row justify-between items-center z-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Application Management</h2>
            <div class="flex items-center gap-4">
                <a href="create_order.php" class="bg-[#0F3D3E] text-white px-5 py-2 rounded shadow hover:bg-green-900 transition flex items-center gap-2">
                    <i class="fas fa-plus-circle"></i> <span class="hidden md:inline">New Application</span>
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-4 md:p-8">
            
            <div class="mb-6 flex gap-4">
                <form method="GET" class="flex-1 relative">
                    <input type="text" name="q" value="<?php echo $search_query; ?>" placeholder="Search by CNIC, Name, Phone, Service..." class="w-full pl-10 pr-4 py-3 rounded-lg shadow-sm border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-600">
                    <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                </form>
                <?php if($search_query): ?>
                    <a href="admin_orders.php" class="bg-gray-200 text-gray-600 px-4 py-3 rounded hover:bg-gray-300">Clear</a>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full leading-normal">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-xs font-bold text-left">
                                <th class="px-5 py-3">Tracking ID (CNIC)</th>
                                <th class="px-5 py-3">Customer Details</th>
                                <th class="px-5 py-3">Service & Status</th>
                                <th class="px-5 py-3">Financials</th>
                                <th class="px-5 py-3 text-center">Docs</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $balance = $row['total_fee'] - $row['paid_amount'];
                                    $balColor = $balance > 0 ? 'text-red-600 font-bold' : 'text-green-600';
                                    $balText = $balance > 0 ? "Due: Rs. ".number_format($balance) : "Paid Full";
                                    $statusBadge = 'bg-yellow-100 text-yellow-800';
                                    if($row['status'] == 'Completed') $statusBadge = 'bg-green-100 text-green-800';
                                    if($row['status'] == 'Cancelled') $statusBadge = 'bg-red-100 text-red-800';
                                    if($row['status'] == 'Ready for Collection') $statusBadge = 'bg-blue-100 text-blue-800';
                            ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-5 py-4 whitespace-no-wrap">
                                    <span class="font-mono font-bold text-[#0F3D3E] bg-green-50 px-2 py-1 rounded"><?php echo $row['tracking_id']; ?></span>
                                    <p class="text-xs text-gray-400 mt-1"><?php echo date("d M Y", strtotime($row['created_at'])); ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-sm font-bold text-gray-900"><?php echo $row['customer_name']; ?></div>
                                    <div class="text-xs text-gray-500"><i class="fas fa-phone mr-1"></i> <?php echo $row['phone']; ?></div>
                                    <?php if($row['cnic'] && $row['cnic'] != $row['tracking_id']): ?><div class="text-xs text-gray-400">CNIC: <?php echo $row['cnic']; ?></div><?php endif; ?>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold mb-2"><?php echo $row['name_en']; ?></p>
                                    <form method="POST" class="flex flex-col gap-2">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <div class="flex gap-1">
                                            <select name="status" class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-green-500 outline-none cursor-pointer bg-white">
                                                <option value="<?php echo $row['status']; ?>" selected class="font-bold bg-gray-100">Current: <?php echo $row['status']; ?></option>
                                                <option value="Documents Verified">Documents Verified</option>
                                                <option value="Submitted to Govt">Submitted to Govt</option>
                                                <option value="Waiting for Sign">Waiting for Sign</option>
                                                <option value="Ready for Collection">Ready for Collection</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" class="text-xs bg-blue-50 text-blue-600 border border-blue-200 px-2 rounded hover:bg-blue-100"><i class="fas fa-check"></i></button>
                                        </div>
                                        <input type="text" name="remarks" value="<?php echo $row['remarks']; ?>" placeholder="Remarks..." class="text-xs border-b border-gray-300 focus:border-green-500 outline-none w-full bg-transparent pb-1">
                                    </form>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="text-xs text-gray-500">Total: Rs. <?php echo number_format($row['total_fee']); ?></div>
                                    <div class="text-xs text-gray-500">Paid: Rs. <?php echo number_format($row['paid_amount']); ?></div>
                                    <div class="text-sm mt-1 <?php echo $balColor; ?>"><?php echo $balText; ?></div>
                                </td>
                                <td class="px-5 py-4 text-center space-y-2">
                                    <a href="invoice.php?id=<?php echo $row['id']; ?>" target="_blank" class="block w-full text-xs border border-gray-300 rounded py-1 hover:bg-gray-100 text-gray-600"><i class="fas fa-file-invoice text-blue-500 mr-1"></i> Invoice</a>
                                    <a href="receipt.php?id=<?php echo $row['id']; ?>" target="_blank" class="block w-full text-xs border border-gray-300 rounded py-1 hover:bg-gray-100 text-gray-600"><i class="fas fa-receipt text-gray-500 mr-1"></i> Slip</a>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="admin_orders.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure?');" class="text-red-400 hover:text-red-600 p-2"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } } else { ?>
                            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500"><i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i><p>No orders found matching your search.</p></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>