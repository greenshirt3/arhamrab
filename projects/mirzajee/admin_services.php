<?php
session_start();
include 'config.php';
if (!isset($_SESSION['admin_logged_in'])) header("Location: login.php");

if (isset($_POST['add_service'])) {
    $en = cleanInput($_POST['name_en']);
    $ur = cleanInput($_POST['name_ur']);
    $price = cleanInput($_POST['price']);
    $conn->query("INSERT INTO services (name_en, name_ur, price) VALUES ('$en', '$ur', '$price')");
}

if (isset($_POST['update_service'])) {
    $id = cleanInput($_POST['id']);
    $price = cleanInput($_POST['price']);
    $conn->query("UPDATE services SET price='$price' WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col md:flex-row font-sans min-h-screen">
    <?php include 'admin_sidebar.php'; ?>
    
    <div class="flex-1 p-8 overflow-x-hidden">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Service Catalog</h1>

        <div class="bg-white p-6 rounded shadow mb-8">
            <h3 class="font-bold mb-4 text-green-800">Add New Service</h3>
            <form method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs text-gray-500">Service Name (English)</label>
                    <input type="text" name="name_en" required class="w-full border p-2 rounded">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs text-gray-500">Service Name (Urdu)</label>
                    <input type="text" name="name_ur" required class="w-full border p-2 rounded font-noto">
                </div>
                <div class="w-full md:w-32">
                    <label class="block text-xs text-gray-500">Base Price</label>
                    <input type="number" name="price" required class="w-full border p-2 rounded">
                </div>
                <button type="submit" name="add_service" class="bg-green-700 text-white px-6 py-2 rounded hover:bg-green-800 w-full md:w-auto">Add</button>
            </form>
        </div>

        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="p-4">Service Name</th>
                        <th class="p-4">Urdu Name</th>
                        <th class="p-4">Standard Price</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM services");
                    while($row = $res->fetch_assoc()){
                        echo "<tr class='border-b'>";
                        echo "<td class='p-4'>".$row['name_en']."</td>";
                        echo "<td class='p-4 font-noto'>".$row['name_ur']."</td>";
                        echo "<td class='p-4'>
                                <form method='POST' class='flex items-center gap-2'>
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                    <input type='number' name='price' value='".$row['price']."' class='w-24 border p-1 text-sm rounded'>
                                    <button type='submit' name='update_service' class='text-blue-600 text-sm'><i class='fas fa-save'></i></button>
                                </form>
                              </td>";
                        echo "<td class='p-4 text-gray-400'><i class='fas fa-lock'></i></td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>