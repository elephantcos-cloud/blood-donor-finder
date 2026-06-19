<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/includes/auth.php';

$page_title = "জরুরি রিকোয়েস্ট ম্যানেজমেন্ট";
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify()) {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($action === 'approve') {
        $conn->query("UPDATE emergency_requests SET status='active' WHERE id = $id");
        $msg = "রিকোয়েস্ট অনুমোদন করা হয়েছে, এখন সাইটে দেখা যাবে।";
    } elseif ($action === 'fulfill') {
        $conn->query("UPDATE emergency_requests SET status='fulfilled' WHERE id = $id");
        $msg = "রিকোয়েস্ট ফুলফিল্ড হিসেবে চিহ্নিত করা হয়েছে।";
    } elseif ($action === 'delete') {
        $conn->query("DELETE FROM emergency_requests WHERE id = $id");
        $msg = "রিকোয়েস্ট ডিলিট করা হয়েছে।";
    }
}

$requests = $conn->query("SELECT * FROM emergency_requests ORDER BY FIELD(status,'pending','active','fulfilled','expired'), created_at DESC LIMIT 100");

require __DIR__ . '/includes/header.php';
?>

<?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>

<div class="card">
    <h3>সব জরুরি রিকোয়েস্ট</h3>
    <?php while ($r = $requests->fetch_assoc()): ?>
        <div class="list-item">
            <h4><?php echo htmlspecialchars($r['patient_name']); ?> <span class="bg-badge"><?php echo $r['blood_group']; ?></span>
                <span class="badge badge-<?php echo $r['urgency_level']; ?>"><?php echo ['normal'=>'সাধারণ','urgent'=>'জরুরি','critical'=>'খুবই জরুরি'][$r['urgency_level']]; ?></span>
                <span class="muted">[<?php echo $r['status']; ?>]</span>
            </h4>
            <div class="meta">🏥 <?php echo htmlspecialchars($r['hospital_name']); ?> &middot; <?php echo htmlspecialchars($r['district']); ?> &middot; ☎ <?php echo htmlspecialchars($r['contact_number']); ?></div>
            <div style="margin-top:8px; display:flex; gap:10px;">
                <?php if ($r['status'] === 'pending'): ?>
                <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><input type="hidden" name="action" value="approve">
                    <button type="submit" class="btn" style="width:auto;padding:8px 16px;margin:0;">অনুমোদন করো</button>
                </form>
                <?php endif; ?>
                <?php if ($r['status'] === 'active'): ?>
                <form method="post"><?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><input type="hidden" name="action" value="fulfill">
                    <button type="submit" class="btn btn-outline" style="width:auto;padding:8px 16px;margin:0;">ফুলফিল্ড করো</button>
                </form>
                <?php endif; ?>
                <form method="post" onsubmit="return confirm('ডিলিট করতে চাও?');"><?php echo csrf_field(); ?><input type="hidden" name="id" value="<?php echo $r['id']; ?>"><input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn-outline" style="border:none;background:none;color:var(--red-dark);cursor:pointer;padding:8px;">ডিলিট</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
