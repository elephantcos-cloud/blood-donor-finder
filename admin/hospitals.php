<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/includes/locations.php';
require __DIR__ . '/includes/auth.php';

$page_title = "হাসপাতাল ম্যানেজমেন্ট";
$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    if (($_POST['action'] ?? '') === 'delete') {
        $id = (int)$_POST['id'];
        $conn->query("DELETE FROM hospitals WHERE id = $id");
        $msg = "ডিলিট করা হয়েছে।";
    } else {
        $name = trim($_POST['name'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $division = $_POST['division'] ?? '';
        $district = $_POST['district'] ?? '';
        $has_blood_bank = isset($_POST['has_blood_bank']) ? 1 : 0;
        $map_link = trim($_POST['map_link'] ?? '');

        if ($name === '' || $address === '' || $division === '' || $district === '') {
            $error = "নাম, ঠিকানা, বিভাগ ও জেলা আবশ্যক।";
        } else {
            $stmt = $conn->prepare("INSERT INTO hospitals (name, address, phone, division, district, has_blood_bank, map_link) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssis", $name, $address, $phone, $division, $district, $has_blood_bank, $map_link);
            $stmt->execute();
            $msg = "হাসপাতাল যুক্ত করা হয়েছে।";
        }
    }
}

$hospitals = $conn->query("SELECT * FROM hospitals ORDER BY created_at DESC");

require __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h3>নতুন হাসপাতাল যুক্ত করো</h3>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>হাসপাতালের নাম</label>
        <input type="text" name="name" required>
        <label>ঠিকানা</label>
        <input type="text" name="address" required>
        <label>ফোন নম্বর</label>
        <input type="text" name="phone">
        <?php $loc_required = true; require __DIR__ . '/../includes/location_select.php'; ?>
        <label>Google Maps লিংক (ঐচ্ছিক)</label>
        <input type="text" name="map_link">
        <label><input type="checkbox" name="has_blood_bank" style="width:auto;display:inline-block;"> এখানে ব্লাড ব্যাংক আছে</label>
        <button type="submit" class="btn">যুক্ত করো</button>
    </form>
</div>

<div class="card">
    <h3>সব হাসপাতাল</h3>
    <table class="admin-table">
        <tr><th>নাম</th><th>জেলা</th><th>ফোন</th><th>একশন</th></tr>
        <?php while ($h = $hospitals->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($h['name']); ?></td>
            <td><?php echo htmlspecialchars($h['district']); ?></td>
            <td><?php echo htmlspecialchars($h['phone'] ?? ''); ?></td>
            <td>
                <form method="post" onsubmit="return confirm('ডিলিট করতে চাও?');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="id" value="<?php echo $h['id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn-outline" style="border:none;background:none;color:var(--red-dark);cursor:pointer;padding:0;">ডিলিট</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
