<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';

$page_title = "হাসপাতাল ডিরেক্টরি - রক্তবন্ধন";
$district_filter = $_GET['district'] ?? '';

if ($district_filter !== '') {
    $stmt = $conn->prepare("SELECT * FROM hospitals WHERE district = ? ORDER BY name");
    $stmt->bind_param("s", $district_filter);
    $stmt->execute();
    $hospitals = $stmt->get_result();
} else {
    $hospitals = $conn->query("SELECT * FROM hospitals ORDER BY district, name LIMIT 100");
}

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>হাসপাতাল ডিরেক্টরি</h1>
    <p>হাসপাতাল ও ব্লাড ব্যাংকের তথ্য খুঁজুন</p>
</div>

<div class="card">
    <form method="get">
        <label>জেলা দিয়ে ফিল্টার করো</label>
        <select name="district" onchange="this.form.submit()">
            <option value="">সব জেলা</option>
            <?php
            $all_districts = [];
            foreach ($locations as $div => $districts) {
                foreach (array_keys($districts) as $d) $all_districts[] = $d;
            }
            sort($all_districts);
            foreach ($all_districts as $d): ?>
                <option value="<?php echo $d; ?>" <?php echo $district_filter === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card">
    <?php if ($hospitals->num_rows === 0): ?>
        <p class="muted">এই মুহূর্তে এই এলাকায় কোনো হাসপাতালের তথ্য যুক্ত করা হয়নি। অ্যাডমিন প্যানেল থেকে যুক্ত করা যাবে।</p>
    <?php else: ?>
        <?php while ($h = $hospitals->fetch_assoc()): ?>
            <div class="list-item">
                <h4><?php echo htmlspecialchars($h['name']); ?> <?php if ($h['has_blood_bank']): ?><span class="badge badge-normal">ব্লাড ব্যাংক আছে</span><?php endif; ?></h4>
                <div class="meta">📍 <?php echo htmlspecialchars($h['address']); ?>, <?php echo htmlspecialchars($h['district']); ?></div>
                <?php if ($h['phone']): ?><div class="meta">☎ <?php echo htmlspecialchars($h['phone']); ?></div><?php endif; ?>
                <div style="margin-top:8px; display:flex; gap:8px;">
                    <?php if ($h['phone']): ?><a href="tel:<?php echo htmlspecialchars($h['phone']); ?>" class="btn btn-outline" style="width:auto; padding:8px 16px;">কল করো</a><?php endif; ?>
                    <?php if ($h['map_link']): ?><a href="<?php echo htmlspecialchars($h['map_link']); ?>" target="_blank" class="btn btn-outline" style="width:auto; padding:8px 16px;">ম্যাপ দেখো</a><?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
