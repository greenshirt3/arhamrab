<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: login.php");

if (isset($_POST['add_expense'])) {
    $desc = cleanInput($_POST['description']);
    $amount = cleanInput($_POST['amount']);
    $conn->query("INSERT INTO transactions (type, category, amount, description) VALUES ('Expense', 'Office Expense', '$amount', '$desc')");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Finance & Accounts</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 flex flex-col md:flex-row font-sans min-h-screen">
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="flex-1 p-8 overflow-x-hidden">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Financial Ledger</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded shadow p-6">
                <h3 class="font-bold text-lg mb-4 text-gray-700">Recent Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="p-2">Date</th>
                                <th class="p-2">Description</th>
                                <th class="p-2 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT id, 'Income' as type, CONCAT('Order #', tracking_id) as descr, paid_amount as amt, created_at FROM orders WHERE paid_amount > 0 UNION ALL SELECT id, type, description, amount, date FROM transactions WHERE type='Expense' ORDER BY created_at DESC LIMIT 10";
                            $res = $conn->query($sql);
                            while($row = $res->fetch_assoc()){
                                $color = $row['type'] == 'Income' ? 'text-green-600' : 'text-red-600';
                                $sign = $row['type'] == 'Income' ? '+' : '-';
                                echo "<tr class='border-b'>";
                                echo "<td class='p-2 text-gray-500'>".date('d M', strtotime($row['created_at']))."</td>";
                                echo "<td class='p-2'>".$row['descr']."</td>";
                                echo "<td class='p-2 text-right font-bold $color'>$sign ".$row['amt']."</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded shadow p-6 h-fit">
                <h3 class="font-bold text-lg mb-4 text-red-700">Record Office Expense</h3>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600">Description</label>
                        <input type="text" name="description" placeholder="e.g. Electricity Bill, Tea, Paper" class="w-full border p-2 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600">Amount</label>
                        <input type="number" name="amount" class="w-full border p-2 rounded" required>
                    </div>
                    <button type="submit" name="add_expense" class="w-full bg-red-600 text-white py-2 rounded font-bold hover:bg-red-700">Record Expense</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>