<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/includes/auth.php';

$page_title = "ডোনার ম্যানেজমেন্ট";
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'toggle_block') {
        $conn->query("UPDATE donors SET is_blocked = 1 - is_blocked WHERE id = $id");
        $msg = "স্ট্যাটাস পরিবর্তন হয়েছে।";
    } elseif ($action === 'toggle_verify') {
        $conn->query("UPDATE donors SET is_verified = 1 - is_verified WHERE id = $id");
        $msg = "ভেরিফিকেশন স্ট্যাটাস পরিবর্তন হয়েছে।";
    } elseif ($action === 'delete') {
        $conn->query("DELETE FROM donors WHERE id = $id");
        $msg = "ডোনার ডিলিট করা হয়েছে।";
    }
}

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $like = '%' . $conn->real_escape_string($search) . '%';
    $donors = $conn->query("SELECT * FROM donors WHERE name LIKE '$like' OR phone LIKE '$like' ORDER BY created_at DESC LIMIT 100");
} else {
    $donors = $conn->query("SELECT * FROM donors ORDER BY created_at DESC LIMIT 100");
}

require __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>

<div class="card">
    <form method="get">
        <label>নাম বা মোবাইল নম্বর দিয়ে খুঁজো</label>
        <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn">খুঁজো</button>
    </form>
</div>

<div class="card">
    <h3>ডোনার তালিকা (সাম্প্রতিক ১০০)</h3>
    <table class="admin-table">
        <tr><th>নাম</th><th>ফোন</th><th>গ্রুপ</th><th>জেলা</th><th>স্ট্যাটাস</th><th>একশন</th></tr>
        <?php while ($d = $donors->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($d['name']); ?><?php echo $d['is_verified'] ? ' ✓' : ''; ?></td>
            <td><?php echo htmlspecialchars($d['phone']); ?></td>
            <td><?php echo htmlspecialchars($d['blood_group']); ?></td>
            <td><?php echo htmlspecialchars($d['district']); ?></td>
            <td><?php echo $d['is_blocked'] ? '<span class="badge badge-critical">ব্লক</span>' : '<span class="badge badge-normal">সক্রিয়</span>'; ?></td>
            <td class="actions">
                <form method="post" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                    <input type="hidden" name="action" value="toggle_verify">
                    <button type="submit" class="btn-outline" style="border:none;background:none;color:var(--red);cursor:pointer;padding:0;">ভেরিফাই</button>
                </form>
                <form method="post" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                    <input type="hidden" name="action" value="toggle_block">
                    <button type="submit" class="btn-outline" style="border:none;background:none;color:var(--red);cursor:pointer;padding:0;"><?php echo $d['is_blocked'] ? 'আনব্লক' : 'ব্লক'; ?></button>
                </form>
                <form method="post" style="display:inline;" onsubmit="return confirm('সত্যিই ডিলিট করতে চাও? এটা ফিরিয়ে আনা যাবে না।');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn-outline" style="border:none;background:none;color:var(--red-dark);cursor:pointer;padding:0;">ডিলিট</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
