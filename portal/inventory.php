<?php 
require 'includes/header.php'; 

// --- 1. GUARANTEED ACCESS CHECK ---
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$allowed_ids = [1, 14, 15]; // IDs from SQL Dump
$is_admin = false;

if (in_array($_SESSION['user_id'], $allowed_ids)) {
    $is_admin = true;
} elseif (isset($_SESSION['role']) && strtolower(trim($_SESSION['role'])) === 'admin') {
    $is_admin = true;
}

$has_perm = (isset($_SESSION['permissions']['shop']) && $_SESSION['permissions']['shop'] == 1);

if (!$is_admin && !$has_perm) {
    die("<div class='container mt-5'><div class='alert alert-danger text-center p-5 shadow'>⛔ ACCESS DENIED (Inventory)</div></div>");
}

// --- 2. ADMIN ACTIONS ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Safety check for Edit/Delete
    if (isset($_POST['update_item']) || isset($_POST['delete_item'])) {
        if (!$is_admin) {
            die("<script>alert('⛔ Access Denied: Only Admin can modify items.'); window.location='inventory.php';</script>");
        }
    }

    if (isset($_POST['update_item'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $pdo->prepare("UPDATE inventory SET item_name=?, sale_price=?, stock_qty=? WHERE id=?")->execute([$name, $price, $stock, $id]);
        echo "<div class='alert alert-success'>Item Updated!</div>";
    }

    if (isset($_POST['delete_item'])) {
        $id = $_POST['delete_item'];
        $pdo->prepare("DELETE FROM inventory WHERE id=?")->execute([$id]);
        echo "<div class='alert alert-warning'>Item Deleted!</div>";
    }
}

$items = $pdo->query("SELECT * FROM inventory ORDER BY item_name ASC")->fetchAll();
$total_value = $pdo->query("SELECT SUM(sale_price * stock_qty) FROM inventory WHERE stock_qty > 0")->fetchColumn();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-boxes text-primary"></i> Inventory Manager</h3>
    <?php if($is_admin): ?>
    <div class="bg-white p-2 px-3 rounded-pill shadow-sm border">
        <small class="text-muted fw-bold">TOTAL STOCK VALUE:</small>
        <span class="text-success fw-bold">Rs. <?php echo number_format($total_value); ?></span>
    </div>
    <?php endif; ?>
</div>

<div class="glass-panel p-0 overflow-hidden">
    <table class="table table-hover mb-0 align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th class="ps-4">Item Name</th>
                <th>Stock Level</th>
                <th>Sale Price</th>
                <?php if($is_admin): ?><th class="text-end pe-4">Actions</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $i): ?>
            <tr>
                <td class="ps-4 fw-bold"><?php echo $i['item_name']; ?></td>
                <td>
                    <?php 
                    if($i['stock_qty'] < 5) echo '<span class="badge bg-danger">Low: '.$i['stock_qty'].'</span>';
                    elseif($i['stock_qty'] < 0) echo '<span class="badge bg-secondary">Unlimited</span>';
                    else echo '<span class="badge bg-success">In Stock: '.$i['stock_qty'].'</span>';
                    ?>
                </td>
                <td class="text-primary fw-bold">Rs. <?php echo number_format($i['sale_price']); ?></td>
                
                <?php if($is_admin): ?>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-primary me-1" onclick='editItem(<?php echo json_encode($i); ?>)'>
                        <i class="fas fa-edit"></i>
                    </button>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this item?');">
                        <input type="hidden" name="delete_item" value="<?php echo $i['id']; ?>">
                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if($is_admin): ?>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-panel border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Edit Item</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="update_item" value="1">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label>Item Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label>Sale Price</label>
                            <input type="number" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label>Stock Qty</label>
                            <input type="number" name="stock" id="edit_stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3 text-end">
                        <button class="btn btn-success w-100 rounded-pill">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let editModal = new bootstrap.Modal(document.getElementById('editModal'));
function editItem(item) {
    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_name').value = item.item_name;
    document.getElementById('edit_price').value = item.sale_price;
    document.getElementById('edit_stock').value = item.stock_qty;
    editModal.show();
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>