<?php
require_once '../config/database.php';

// Ensure session started (database.php already starts session if needed)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin user
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    redirect('../login.php', 'Please login as admin', true);
}

$database = new Database();
$db = $database->getConnection();

// Safe helpers: attempt queries, fall back to 0 on error
function safe_count($db, $table) {
    try {
        $stmt = $db->query("SELECT COUNT(*) FROM `" . $table . "`");
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

function safe_sum_candidates($db, $candidates) {
    foreach ($candidates as $candidate) {
        list($col, $table) = explode('|', $candidate);
        try {
            $stmt = $db->query("SELECT SUM($col) FROM `" . $table . "`");
            $val = $stmt->fetchColumn();
            if ($val !== null) return (float) $val;
        } catch (PDOException $e) {
            continue;
        }
    }
    return 0.0;
}

$totalUsers = safe_count($db, 'users');
$totalOrders = safe_count($db, 'orders');
$totalProducts = safe_count($db, 'products');
$revenue = safe_sum_candidates($db, ['total_amount|orders', 'amount|orders', 'price|orders']);

// Include site header (keeps consistent nav/styles)
include_once __DIR__ . '/../includes/header.php';
?>

<main class="admin-container" style="padding:28px 16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <h1 style="margin:0;font-size:1.6rem;">Admin Dashboard</h1>
        <div style="display:flex;gap:8px;align-items:center;">
            <a href="create_product.php" class="btn" style="background:#ff6b6b;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;">+ Add Product</a>
            <a href="orders.php" class="btn" style="background:#556; color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;">View Orders</a>
        </div>
    </div>

    <section style="margin-top:18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:18px;">
        <div class="stat-card" style="background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
            <h4 style="margin:0 0 6px 0;color:#333;">Total Users</h4>
            <p style="font-size:1.4rem;font-weight:600;margin:0;color:#111;"><?php echo htmlspecialchars($totalUsers); ?></p>
        </div>

        <div class="stat-card" style="background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
            <h4 style="margin:0 0 6px 0;color:#333;">Total Orders</h4>
            <p style="font-size:1.4rem;font-weight:600;margin:0;color:#111;"><?php echo htmlspecialchars($totalOrders); ?></p>
        </div>

        <div class="stat-card" style="background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
            <h4 style="margin:0 0 6px 0;color:#333;">Revenue</h4>
            <p style="font-size:1.4rem;font-weight:600;margin:0;color:#111;">&#36;<?php echo number_format($revenue, 2); ?></p>
        </div>

        <div class="stat-card" style="background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06);">
            <h4 style="margin:0 0 6px 0;color:#333;">Products</h4>
            <p style="font-size:1.4rem;font-weight:600;margin:0;color:#111;"><?php echo htmlspecialchars($totalProducts); ?></p>
        </div>
    </section>

    <section style="margin-top:22px;">
        <h3 style="margin:0 0 12px 0;">Quick Actions</h3>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="create_product.php" class="btn" style="padding:10px 14px;border-radius:8px;background:#ff6b6b;color:#fff;text-decoration:none;">Add Product</a>
            <a href="orders.php" class="btn" style="padding:10px 14px;border-radius:8px;background:#556;color:#fff;text-decoration:none;">View Orders</a>
            <a href="users.php" class="btn" style="padding:10px 14px;border-radius:8px;background:#6c757d;color:#fff;text-decoration:none;">Manage Users</a>
        </div>
    </section>
</main>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>