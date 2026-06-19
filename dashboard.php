<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';

if (!isset($_SESSION['donor_id'])) {
    redirect('/login.php');
}

$page_title = "আমার প্রোফাইল - রক্তবন্ধন";
$donor_id = $_SESSION['donor_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
    $error = "ফর্ম সেশন মেয়াদোত্তীর্ণ হয়েছে, আবার চেষ্টা করুন।";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'mark_donated') {
    // ---------- "আজ রক্ত দিয়েছি" বাটন ----------
    $stmt = $conn->prepare("UPDATE donors SET last_donation_date = CURDATE(), total_donations = total_donations + 1 WHERE id = ?");
    $stmt->bind_param("i", $donor_id);
    $stmt->execute();
    $stmt->close();
    $success = "অভিনন্দন! রক্তদানের তথ্য আপডেট হয়েছে। তোমার পরবর্তী ১২০ দিন বিরতি শুরু হলো।";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_group = $_POST['blood_group'] ?? '';
    $division = $_POST['division'] ?? '';
    $district = $_POST['district'] ?? '';
    $upazila = trim($_POST['upazila'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $weight = $_POST['weight_kg'] ?? null;
    $emergency_contact = trim($_POST['emergency_contact'] ?? '');
    if ($weight === '') $weight = null;

    $stmt = $conn->prepare(
        "UPDATE donors SET blood_group=?, division=?, district=?, upazila=?, address=?, whatsapp=?, occupation=?, weight_kg=?, emergency_contact=? WHERE id=?"
    );
    $stmt->bind_param("sssssssdsi", $blood_group, $division, $district, $upazila, $address, $whatsapp, $occupation, $weight, $emergency_contact, $donor_id);
    if ($stmt->execute()) {
        $success = "প্রোফাইল আপডেট হয়েছে।";
    } else {
        $error = "আপডেট করা যায়নি, আবার চেষ্টা করুন।";
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->bind_param("i", $donor_id);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$stmt->close();

$days_left = days_until_eligible($donor['last_donation_date']);
$progress_pct = $days_left > 0 ? round((1 - $days_left / 120) * 100) : 100;

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>স্বাগতম, <?php echo htmlspecialchars($donor['name']); ?></h1>
    <p class="muted"><?php echo htmlspecialchars($donor['phone']); ?> &middot; মোট রক্তদান: <?php echo (int)$donor['total_donations']; ?> বার</p>
    <div class="hero-actions">
        <a href="/blood-card.php" class="btn">আমার ব্লাড কার্ড দেখো</a>
    </div>
</div>

<?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div class="card">
    <h3>রক্তদানের অবস্থা</h3>
    <?php if ($days_left === 0): ?>
        <p class="available">● তুমি এখন রক্ত দিতে সক্ষম</p>
        <form method="post" onsubmit="return confirm('তুমি কি আজ রক্ত দিয়েছ? এটা নিশ্চিত করলে পরবর্তী ১২০ দিন তোমাকে \'অনুপলব্ধ\' দেখানো হবে।');">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="mark_donated">
            <button type="submit" class="btn">আজ রক্ত দিয়েছি</button>
        </form>
    <?php else: ?>
        <p class="unavailable-tag">আর <?php echo $days_left; ?> দিন পর আবার রক্ত দিতে পারবে</p>
        <div class="progress-wrap"><div class="progress-bar cooldown" style="width:<?php echo $progress_pct; ?>%"></div></div>
    <?php endif; ?>
    <p class="muted" style="margin-top:10px;">সর্বশেষ রক্তদান: <?php echo $donor['last_donation_date'] ? htmlspecialchars($donor['last_donation_date']) : 'এখনো দেওয়া হয়নি'; ?></p>
</div>

<div class="card">
    <h3>প্রোফাইল সম্পাদনা</h3>
    <form method="post">
        <?php echo csrf_field(); ?>
        <div class="row2">
            <div>
                <label>ব্লাড গ্রুপ</label>
                <select name="blood_group" required>
                    <?php foreach ($blood_groups as $bg): ?>
                        <option value="<?php echo $bg; ?>" <?php echo $donor['blood_group'] === $bg ? 'selected' : ''; ?>><?php echo $bg; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>WhatsApp নম্বর</label>
                <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($donor['whatsapp'] ?? ''); ?>">
            </div>
        </div>

        <?php
        $loc_division = $donor['division'];
        $loc_district = $donor['district'];
        $loc_upazila = $donor['upazila'];
        require __DIR__ . '/includes/location_select.php';
        ?>

        <label>সম্পূর্ণ ঠিকানা</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($donor['address'] ?? ''); ?>">

        <div class="row2">
            <div>
                <label>পেশা</label>
                <input type="text" name="occupation" value="<?php echo htmlspecialchars($donor['occupation'] ?? ''); ?>">
            </div>
            <div>
                <label>ওজন (কেজি)</label>
                <input type="number" step="0.1" name="weight_kg" value="<?php echo htmlspecialchars((string)($donor['weight_kg'] ?? '')); ?>">
            </div>
        </div>

        <label>জরুরি যোগাযোগ নম্বর</label>
        <input type="text" name="emergency_contact" value="<?php echo htmlspecialchars($donor['emergency_contact'] ?? ''); ?>">

        <button type="submit" class="btn">আপডেট করুন</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
