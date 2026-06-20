<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';
require __DIR__ . '/includes/icons.php';
require __DIR__ . '/includes/avatar.php';

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
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_account') {
    // ---------- অ্যাকাউন্ট স্থায়ীভাবে ডিলিট — নিরাপত্তার জন্য পাসওয়ার্ড আবার যাচাই করা হয় ----------
    $confirm_password = $_POST['confirm_password'] ?? '';
    $stmt = $conn->prepare("SELECT password, photo_path FROM donors WHERE id = ?");
    $stmt->bind_param("i", $donor_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row && password_verify($confirm_password, $row['password'])) {
        if (!empty($row['photo_path'])) {
            @unlink(__DIR__ . '/' . $row['photo_path']);
        }
        $del = $conn->prepare("DELETE FROM donors WHERE id = ?");
        $del->bind_param("i", $donor_id);
        $del->execute();
        $del->close();
        session_destroy();
        redirect('/index.php?account_deleted=1');
    } else {
        $error = "পাসওয়ার্ড মিলছে না, অ্যাকাউন্ট ডিলিট করা যায়নি।";
    }
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

    // ---------- প্রোফাইল ছবি আপডেট (ঐচ্ছিক) ----------
    $photo_sql = '';
    $new_photo_path = null;
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $info = @getimagesize($_FILES['photo']['tmp_name']);
        if ($info !== false && isset($allowed[$info['mime']]) && $_FILES['photo']['size'] <= 2 * 1024 * 1024) {
            $ext = $allowed[$info['mime']];
            $filename = bin2hex(random_bytes(12)) . '.' . $ext;
            $dest = __DIR__ . '/uploads/donor_photos/' . $filename;
            if (@move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                $new_photo_path = 'uploads/donor_photos/' . $filename;
            }
        } else {
            $error = "ছবি শুধু JPG/PNG ফরম্যাটে এবং ২MB-এর কম হতে হবে।";
        }
    }

    if ($error === '') {
        if ($new_photo_path !== null) {
            $stmt = $conn->prepare(
                "UPDATE donors SET blood_group=?, division=?, district=?, upazila=?, address=?, whatsapp=?, occupation=?, weight_kg=?, emergency_contact=?, photo_path=? WHERE id=?"
            );
            $stmt->bind_param("sssssssdssi", $blood_group, $division, $district, $upazila, $address, $whatsapp, $occupation, $weight, $emergency_contact, $new_photo_path, $donor_id);
        } else {
            $stmt = $conn->prepare(
                "UPDATE donors SET blood_group=?, division=?, district=?, upazila=?, address=?, whatsapp=?, occupation=?, weight_kg=?, emergency_contact=? WHERE id=?"
            );
            $stmt->bind_param("sssssssdsi", $blood_group, $division, $district, $upazila, $address, $whatsapp, $occupation, $weight, $emergency_contact, $donor_id);
        }
        if ($stmt->execute()) {
            $success = "প্রোফাইল আপডেট হয়েছে।";
        } else {
            $error = "আপডেট করা যায়নি, আবার চেষ্টা করুন।";
        }
        $stmt->close();
    }
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
    <div style="display:flex; justify-content:center; margin-bottom:10px;">
        <?php echo avatar_html($donor['photo_path'] ?? null, $donor['name'], $donor['blood_group'], 72); ?>
    </div>
    <h1>স্বাগতম, <?php echo htmlspecialchars($donor['name']); ?></h1>
    <p class="muted"><?php echo htmlspecialchars($donor['phone']); ?> &middot; মোট রক্তদান: <?php echo (int)$donor['total_donations']; ?> বার</p>
    <div class="hero-actions">
        <a href="/blood-card.php" class="btn">আমার ব্লাড কার্ড দেখো</a>
        <a href="/logout.php" class="btn btn-outline"><?php echo icon('logout', 'ic-sm'); ?> লগ-আউট</a>
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
    <form method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <label>প্রোফাইল ছবি পরিবর্তন (ঐচ্ছিক, সর্বোচ্চ ২MB)</label>
        <input type="file" name="photo" accept="image/jpeg,image/png">

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

<div class="card" style="border-color:#F0C7C7;">
    <h3 style="color:var(--red-dark);"><?php echo icon('trash', 'ic-sm'); ?> অ্যাকাউন্ট ডিলিট করো</h3>
    <p class="muted">এটা স্থায়ী — ডিলিট করলে তোমার সব তথ্য (প্রোফাইল, রক্তদানের হিস্টোরি) চিরতরে মুছে যাবে, ফিরিয়ে আনা যাবে না।</p>
    <form method="post" onsubmit="return confirm('তুমি কি নিশ্চিত? এই অ্যাকাউন্ট স্থায়ীভাবে ডিলিট হয়ে যাবে।');">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="action" value="delete_account">
        <label>নিশ্চিত করতে তোমার পাসওয়ার্ড দাও</label>
        <input type="password" name="confirm_password" required>
        <button type="submit" class="btn" style="background:var(--red-dark);">অ্যাকাউন্ট স্থায়ীভাবে ডিলিট করো</button>
    </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
