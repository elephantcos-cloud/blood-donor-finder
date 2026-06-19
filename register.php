<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';

$page_title = "রেজিস্ট্রেশন - রক্তবন্ধন";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = "ফর্ম সেশন মেয়াদোত্তীর্ণ হয়েছে, আবার চেষ্টা করুন।";
    } else {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $whatsapp = trim($_POST['whatsapp'] ?? '');
        $password = $_POST['password'] ?? '';
        $blood_group = $_POST['blood_group'] ?? '';
        $division = $_POST['division'] ?? '';
        $district = $_POST['district'] ?? '';
        $upazila = trim($_POST['upazila'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $dob = $_POST['date_of_birth'] ?? null;
        $gender = $_POST['gender'] ?? '';
        $occupation = trim($_POST['occupation'] ?? '');
        $weight = $_POST['weight_kg'] ?? null;
        $emergency_contact = trim($_POST['emergency_contact'] ?? '');

        if ($dob === '') $dob = null;
        if ($weight === '') $weight = null;

        if ($name === '' || $phone === '' || $password === '' || $blood_group === '' || $division === '' || $district === '' || $gender === '') {
            $error = "তারকা (*) চিহ্নিত ঘরগুলো অবশ্যই পূরণ করুন।";
        } elseif (!preg_match('/^01[0-9]{9}$/', $phone)) {
            $error = "একটি সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (যেমনঃ 01XXXXXXXXX)।";
        } elseif (strlen($password) < 6) {
            $error = "পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।";
        } else {
            $check = $conn->prepare("SELECT id FROM donors WHERE phone = ?");
            $check->bind_param("s", $phone);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "এই মোবাইল নম্বর দিয়ে আগেই রেজিস্ট্রেশন করা হয়েছে। লগইন করুন।";
            } else {
                // ---------- প্রোফাইল ছবি আপলোড (ঐচ্ছিক) ----------
                $photo_path = null;
                if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['photo']['tmp_name']);
                    finfo_close($finfo);
                    if (isset($allowed[$mime]) && $_FILES['photo']['size'] <= 2 * 1024 * 1024) {
                        $ext = $allowed[$mime];
                        $filename = bin2hex(random_bytes(12)) . '.' . $ext;
                        $dest = __DIR__ . '/uploads/donor_photos/' . $filename;
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                            $photo_path = 'uploads/donor_photos/' . $filename;
                        }
                    } else {
                        $error = "ছবি শুধু JPG/PNG ফরম্যাটে এবং ২MB-এর কম হতে হবে।";
                    }
                }

                if ($error === '') {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $public_id = generate_public_id();
                    $stmt = $conn->prepare(
                        "INSERT INTO donors (name, photo_path, phone, whatsapp, password, blood_group, division, district, upazila, address, date_of_birth, gender, occupation, weight_kg, emergency_contact, public_id)
                         VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    );
                    $stmt->bind_param(
                        "sssssssssssssdss",
                        $name, $photo_path, $phone, $whatsapp, $hashed, $blood_group,
                        $division, $district, $upazila, $address, $dob, $gender,
                        $occupation, $weight, $emergency_contact, $public_id
                    );
                    if ($stmt->execute()) {
                        $_SESSION['donor_id'] = $stmt->insert_id;
                        $_SESSION['donor_name'] = $name;
                        redirect('/dashboard.php');
                    } else {
                        $error = "রেজিস্ট্রেশন ব্যর্থ হয়েছে। আবার চেষ্টা করুন।";
                    }
                }
            }
            $check->close();
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>রক্তবন্ধু হয়ে যান</h1>
    <p>রেজিস্ট্রেশন করে আজই কারো জীবন বাঁচানোর সুযোগ তৈরি করুন</p>
</div>

<div class="card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <label>পূর্ণ নাম *</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>

        <label>প্রোফাইল ছবি (ঐচ্ছিক, সর্বোচ্চ ২MB)</label>
        <input type="file" name="photo" accept="image/jpeg,image/png">

        <div class="row2">
            <div>
                <label>মোবাইল নম্বর *</label>
                <input type="text" name="phone" placeholder="01XXXXXXXXX" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
            </div>
            <div>
                <label>WhatsApp নম্বর (ঐচ্ছিক)</label>
                <input type="text" name="whatsapp" placeholder="01XXXXXXXXX" value="<?php echo htmlspecialchars($_POST['whatsapp'] ?? ''); ?>">
            </div>
        </div>

        <label>পাসওয়ার্ড *</label>
        <input type="password" name="password" required>

        <div class="row2">
            <div>
                <label>ব্লাড গ্রুপ *</label>
                <select name="blood_group" required>
                    <option value="">সিলেক্ট করুন</option>
                    <?php foreach ($blood_groups as $bg): ?>
                        <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>লিঙ্গ *</label>
                <select name="gender" required>
                    <option value="">সিলেক্ট করুন</option>
                    <option value="male">পুরুষ</option>
                    <option value="female">নারী</option>
                </select>
            </div>
        </div>

        <?php
        $loc_required = true;
        require __DIR__ . '/includes/location_select.php';
        ?>

        <label>সম্পূর্ণ ঠিকানা (ঐচ্ছিক)</label>
        <input type="text" name="address" placeholder="বাড়ি/রোড নম্বর, এলাকার নাম">

        <div class="row2">
            <div>
                <label>জন্মতারিখ (ঐচ্ছিক)</label>
                <input type="date" name="date_of_birth">
            </div>
            <div>
                <label>ওজন (কেজি, ঐচ্ছিক)</label>
                <input type="number" step="0.1" name="weight_kg" placeholder="৫০+ হতে হবে">
            </div>
        </div>

        <div class="row2">
            <div>
                <label>পেশা (ঐচ্ছিক)</label>
                <input type="text" name="occupation">
            </div>
            <div>
                <label>জরুরি যোগাযোগ নম্বর (ঐচ্ছিক)</label>
                <input type="text" name="emergency_contact">
            </div>
        </div>

        <button type="submit" class="btn">রেজিস্ট্রেশন করুন</button>
    </form>
    <p class="muted" style="margin-top:14px;">আগে থেকে অ্যাকাউন্ট আছে? <a href="/login.php">লগইন করুন</a></p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
