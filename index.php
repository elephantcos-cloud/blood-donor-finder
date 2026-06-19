<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';

$page_title = "রক্তবন্ধন - বাংলাদেশের রক্তদাতা খুঁজুন";
$results = [];
$searched = false;

if (isset($_GET['search'])) {
    $searched = true;
    $blood_group = $_GET['blood_group'] ?? '';
    $district = $_GET['district'] ?? '';
    $upazila = $_GET['upazila'] ?? '';

    if ($upazila !== '') {
        $stmt = $conn->prepare(
            "SELECT name, blood_group, district, upazila, phone, gender, last_donation_date
             FROM donors WHERE blood_group=? AND district=? AND upazila=? AND is_blocked=0 ORDER BY name"
        );
        $stmt->bind_param("sss", $blood_group, $district, $upazila);
    } else {
        $stmt = $conn->prepare(
            "SELECT name, blood_group, district, upazila, phone, gender, last_donation_date
             FROM donors WHERE blood_group=? AND district=? AND is_blocked=0 ORDER BY name"
        );
        $stmt->bind_param("ss", $blood_group, $district);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}

// ---------- লাইভ স্ট্যাটিস্টিকস ----------
$total_donors = $conn->query("SELECT COUNT(*) c FROM donors WHERE is_blocked=0")->fetch_assoc()['c'];
$available_donors = $conn->query("SELECT COUNT(*) c FROM donors WHERE is_blocked=0 AND (last_donation_date IS NULL OR DATEDIFF(CURDATE(), last_donation_date) >= 120)")->fetch_assoc()['c'];
$active_requests = $conn->query("SELECT COUNT(*) c FROM emergency_requests WHERE status='active'")->fetch_assoc()['c'];
$district_coverage = $conn->query("SELECT COUNT(DISTINCT district) c FROM donors")->fetch_assoc()['c'];

$bg_stats = [];
$bg_result = $conn->query("SELECT blood_group, COUNT(*) c FROM donors WHERE is_blocked=0 GROUP BY blood_group");
while ($row = $bg_result->fetch_assoc()) {
    $bg_stats[$row['blood_group']] = $row['c'];
}

$recent_donors = $conn->query("SELECT name, blood_group, district FROM donors WHERE is_blocked=0 ORDER BY created_at DESC LIMIT 6");

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>প্রয়োজনে রক্তদাতা খুঁজে নিন</h1>
    <p>বিভাগ, জেলা ও উপজেলা দিয়ে নিকটস্থ রক্তবন্ধু খুঁজুন</p>
    <div class="hero-actions">
        <a href="/emergency.php?new=1" class="btn">জরুরি রক্ত দরকার</a>
        <a href="/register.php" class="btn btn-outline">রক্তবন্ধু হোন</a>
    </div>
</div>

<div class="card">
    <form method="get">
        <label>ব্লাড গ্রুপ</label>
        <select name="blood_group" required>
            <option value="">সিলেক্ট করুন</option>
            <?php foreach ($blood_groups as $bg): ?>
                <option value="<?php echo $bg; ?>" <?php echo (($_GET['blood_group'] ?? '') === $bg) ? 'selected' : ''; ?>><?php echo $bg; ?></option>
            <?php endforeach; ?>
        </select>

        <?php
        $loc_division = $_GET['division'] ?? '';
        $loc_district = $_GET['district'] ?? '';
        $loc_upazila = $_GET['upazila'] ?? '';
        require __DIR__ . '/includes/location_select.php';
        ?>

        <button type="submit" name="search" value="1" class="btn">খুঁজুন</button>
    </form>
</div>

<?php if ($searched): ?>
<div class="card">
    <h3><?php echo count($results); ?> জন রক্তবন্ধু পাওয়া গেছে</h3>
    <?php if (empty($results)): ?>
        <p class="muted">এই গ্রুপ ও এলাকায় কোনো রক্তবন্ধু পাওয়া যায়নি। অন্য এলাকা চেষ্টা করুন, অথবা জরুরি রক্ত রিকোয়েস্ট পোস্ট করুন।</p>
    <?php else: ?>
        <?php foreach ($results as $r):
            $available = is_donor_available($r['last_donation_date']);
        ?>
            <div class="donor-item">
                <div>
                    <strong><?php echo htmlspecialchars($r['name']); ?></strong>
                    <span class="bg-badge"><?php echo $r['blood_group']; ?></span>
                    <div class="muted"><?php echo htmlspecialchars($r['district']); ?><?php echo $r['upazila'] ? ', ' . htmlspecialchars($r['upazila']) : ''; ?></div>
                    <?php if ($available): ?>
                        <div class="available">● এখন রক্ত দিতে সক্ষম</div>
                    <?php else: ?>
                        <div class="unavailable-tag">আর <?php echo days_until_eligible($r['last_donation_date']); ?> দিন পর রক্ত দিতে পারবেন</div>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($r['gender'] === 'female'): ?>
                        <span class="muted">নারী ডোনার — যোগাযোগের জন্য অ্যাডমিনের সাহায্য নিন</span>
                    <?php else: ?>
                        <a href="tel:<?php echo htmlspecialchars($r['phone']); ?>" class="btn btn-outline" style="width:auto; padding:8px 16px;">কল করুন</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="card">
    <h3>লাইভ পরিসংখ্যান</h3>
    <div class="stat-grid">
        <div class="stat-card"><div class="stat-num"><?php echo $total_donors; ?></div><div class="stat-label">মোট রক্তবন্ধু</div></div>
        <div class="stat-card"><div class="stat-num"><?php echo $available_donors; ?></div><div class="stat-label">এখন উপলব্ধ</div></div>
        <div class="stat-card"><div class="stat-num"><?php echo $active_requests; ?></div><div class="stat-label">সক্রিয় জরুরি রিকোয়েস্ট</div></div>
        <div class="stat-card"><div class="stat-num"><?php echo $district_coverage; ?></div><div class="stat-label">জেলা কভারেজ</div></div>
    </div>
    <?php if (!empty($bg_stats)): ?>
        <div class="bg-stats">
            <?php foreach ($bg_stats as $bg => $count): ?>
                <span class="bg-stat-pill"><?php echo $bg; ?>: <?php echo $count; ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($recent_donors->num_rows > 0): ?>
<div class="card">
    <h3>সাম্প্রতিক রক্তবন্ধু</h3>
    <?php while ($rd = $recent_donors->fetch_assoc()): ?>
        <div class="donor-item">
            <div>
                <strong><?php echo htmlspecialchars($rd['name']); ?></strong>
                <span class="bg-badge"><?php echo $rd['blood_group']; ?></span>
                <div class="muted"><?php echo htmlspecialchars($rd['district']); ?></div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<?php endif; ?>

<div class="card">
    <h3>রক্তের গ্রুপ সামঞ্জস্য চার্ট (Blood Compatibility)</h3>
    <table class="compat-table">
        <tr><th>রক্তের গ্রুপ</th><th>দান করতে পারবে</th><th>গ্রহণ করতে পারবে</th></tr>
        <tr><td>O-</td><td>সবাইকে</td><td>O-</td></tr>
        <tr><td>O+</td><td>O+, A+, B+, AB+</td><td>O+, O-</td></tr>
        <tr><td>A-</td><td>A-, A+, AB-, AB+</td><td>A-, O-</td></tr>
        <tr><td>A+</td><td>A+, AB+</td><td>A+, A-, O+, O-</td></tr>
        <tr><td>B-</td><td>B-, B+, AB-, AB+</td><td>B-, O-</td></tr>
        <tr><td>B+</td><td>B+, AB+</td><td>B+, B-, O+, O-</td></tr>
        <tr><td>AB-</td><td>AB-, AB+</td><td>AB-, A-, B-, O-</td></tr>
        <tr><td>AB+</td><td>AB+</td><td>সবার কাছ থেকে</td></tr>
    </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
