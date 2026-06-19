<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';

$page_title = "অ্যাম্বুলেন্স ডিরেক্টরি - রক্তবন্ধন";
$district_filter = $_GET['district'] ?? '';

if ($district_filter !== '') {
    $stmt = $conn->prepare("SELECT * FROM ambulances WHERE district = ? ORDER BY name");
    $stmt->bind_param("s", $district_filter);
    $stmt->execute();
    $ambulances = $stmt->get_result();
} else {
    $ambulances = $conn->query("SELECT * FROM ambulances ORDER BY district, name LIMIT 100");
}

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>অ্যাম্বুলেন্স ডিরেক্টরি</h1>
    <p>জরুরি প্রয়োজনে অ্যাম্বুলেন্সের তথ্য</p>
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
    <?php if ($ambulances->num_rows === 0): ?>
        <p class="muted">এই মুহূর্তে এই এলাকায় কোনো অ্যাম্বুলেন্সের তথ্য যুক্ত করা হয়নি।</p>
    <?php else: ?>
        <?php while ($a = $ambulances->fetch_assoc()): ?>
            <div class="list-item">
                <h4><?php echo htmlspecialchars($a['name']); ?></h4>
                <div class="meta">📍 <?php echo htmlspecialchars($a['address'] ?? ''); ?>, <?php echo htmlspecialchars($a['district']); ?></div>
                <div class="meta">☎ <?php echo htmlspecialchars($a['phone']); ?></div>
                <div style="margin-top:8px;">
                    <a href="tel:<?php echo htmlspecialchars($a['phone']); ?>" class="btn btn-outline" style="width:auto; padding:8px 16px;">এখনই কল করো</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
