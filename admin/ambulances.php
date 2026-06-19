<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/includes/locations.php';
require __DIR__ . '/includes/auth.php';

$page_title = "অ্যাম্বুলেন্স ম্যানেজমেন্ট";
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM ambulances WHERE id = $id");
        $msg = "ডিলিট করা হয়েছে।";
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $division = $_POST['division'] ?? '';
        $district = $_POST['district'] ?? '';
        $address = trim($_POST['address'] ?? '');

        if ($name === '' || $phone === '' || $division === '' || $district === '') {
            $error = "নাম, ফোন, বিভাগ ও জেলা আবশ্যক।";
        } else {
            $stmt = $conn->prepare("INSERT INTO ambulances (name, phone, division, district, address) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sssss", $name, $phone, $division, $district, $address);
            $stmt->execute();
            $msg = "অ্যাম্বুলেন্স যুক্ত করা হয়েছে।";
        }
    }
}

$ambulances = $conn->query("SELECT * FROM ambulances ORDER BY created_at DESC");

require __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h3>নতুন অ্যাম্বুলেন্স যুক্ত করো</h3>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>নাম / প্রতিষ্ঠান</label>
        <input type="text" name="name" required>
        <label>ফোন নম্বর</label>
        <input type="text" name="phone" required>
        <?php $loc_required = true; $loc_show_upazila = false; require __DIR__ . '/../includes/location_select.php'; ?>
        <label>ঠিকানা (ঐচ্ছিক)</label>
        <input type="text" name="address">
        <button type="submit" class="btn">যুক্ত করো</button>
    </form>
</div>

<div class="card">
    <h3>সব অ্যাম্বুলেন্স</h3>
    <table class="admin-table">
        <tr><th>নাম</th><th>জেলা</th><th>ফোন</th><th>একশন</th></tr>
        <?php while ($a = $ambulances->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($a['name']); ?></td>
            <td><?php echo htmlspecialchars($a['district']); ?></td>
            <td><?php echo htmlspecialchars($a['phone']); ?></td>
            <td>
                <form method="post" onsubmit="return confirm('ডিলিট করতে চাও?');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn-outline" style="border:none;background:none;color:var(--red-dark);cursor:pointer;padding:0;">ডিলিট</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
