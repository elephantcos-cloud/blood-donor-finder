<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/includes/auth.php';

$page_title = "ড্যাশবোর্ড";

$total_donors = $conn->query("SELECT COUNT(*) c FROM donors")->fetch_assoc()['c'];
$available_donors = $conn->query("SELECT COUNT(*) c FROM donors WHERE is_blocked=0 AND (last_donation_date IS NULL OR DATEDIFF(CURDATE(), last_donation_date) >= 120)")->fetch_assoc()['c'];
$blocked_donors = $conn->query("SELECT COUNT(*) c FROM donors WHERE is_blocked=1")->fetch_assoc()['c'];
$total_donations = $conn->query("SELECT SUM(total_donations) c FROM donors")->fetch_assoc()['c'] ?? 0;
$pending_requests = $conn->query("SELECT COUNT(*) c FROM emergency_requests WHERE status='pending'")->fetch_assoc()['c'];
$active_requests = $conn->query("SELECT COUNT(*) c FROM emergency_requests WHERE status='active'")->fetch_assoc()['c'];
$new_this_week = $conn->query("SELECT COUNT(*) c FROM donors WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['c'];

$bg_stats = [];
$bg_result = $conn->query("SELECT blood_group, COUNT(*) c FROM donors GROUP BY blood_group");
while ($row = $bg_result->fetch_assoc()) $bg_stats[$row['blood_group']] = $row['c'];

require __DIR__ . '/includes/header.php';
?>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-num"><?php echo $total_donors; ?></div><div class="stat-label">মোট ডোনার</div></div>
    <div class="stat-card"><div class="stat-num"><?php echo $available_donors; ?></div><div class="stat-label">উপলব্ধ ডোনার</div></div>
    <div class="stat-card"><div class="stat-num"><?php echo (int)$total_donations; ?></div><div class="stat-label">মোট রক্তদান</div></div>
    <div class="stat-card"><div class="stat-num"><?php echo $new_this_week; ?></div><div class="stat-label">এই সপ্তাহে নতুন</div></div>
    <div class="stat-card"><div class="stat-num"><?php echo $active_requests; ?></div><div class="stat-label">সক্রিয় রিকোয়েস্ট</div></div>
    <div class="stat-card"><div class="stat-num"><?php echo $pending_requests; ?></div><div class="stat-label">অনুমোদনের অপেক্ষায়</div></div>
    <div class="stat-card"><div class="stat-num"><?php echo $blocked_donors; ?></div><div class="stat-label">ব্লক করা ডোনার</div></div>
</div>

<div class="card">
    <h3>ব্লাড গ্রুপ অ্যানালিটিক্স</h3>
    <div class="bg-stats">
        <?php foreach ($bg_stats as $bg => $count): ?>
            <span class="bg-stat-pill"><?php echo $bg; ?>: <?php echo $count; ?></span>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($pending_requests > 0): ?>
<div class="card">
    <div class="alert alert-error">⚠ <?php echo $pending_requests; ?>টা জরুরি রিকোয়েস্ট অনুমোদনের অপেক্ষায় আছে। <a href="/admin/requests.php">এখনই দেখো</a></div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
