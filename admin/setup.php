<?php
require __DIR__ . '/../config.php';

$page_title = "অ্যাডমিন সেটআপ";
$error = '';
$success = '';

$existing = $conn->query("SELECT COUNT(*) c FROM admins")->fetch_assoc()['c'];

if ($existing > 0) {
    // নিরাপত্তার জন্য: একবার অ্যাডমিন তৈরি হয়ে গেলে এই স্ক্রিপ্ট আর কাজ করবে না।
    $error = "একটা অ্যাডমিন অ্যাকাউন্ট আগে থেকেই আছে। নিরাপত্তার জন্য এই ফাইলটা (admin/setup.php) এখনই FTP দিয়ে সার্ভার থেকে ডিলিট করে দাও।";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = "ফর্ম সেশন মেয়াদোত্তীর্ণ, আবার চেষ্টা করো।";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (strlen($username) < 3 || strlen($password) < 8) {
            $error = "ইউজারনেম কমপক্ষে ৩ অক্ষর এবং পাসওয়ার্ড কমপক্ষে ৮ অক্ষরের হতে হবে।";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);
            if ($stmt->execute()) {
                $success = "অ্যাডমিন অ্যাকাউন্ট তৈরি হয়ে গেছে! এখনই এই admin/setup.php ফাইলটা FTP দিয়ে ডিলিট করে দাও, তারপর /admin/login.php দিয়ে লগইন করো।";
            } else {
                $error = "তৈরি করা যায়নি, আবার চেষ্টা করো।";
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:420px; margin:30px auto;">
    <h3>প্রথম অ্যাডমিন অ্যাকাউন্ট তৈরি করো</h3>
    <p class="muted">এই পেজ দিয়ে শুধু একবারই অ্যাডমিন তৈরি করা যাবে। তৈরি হওয়ার পর এই ফাইলটা সার্ভার থেকে ডিলিট করে দিও, নাহলে যে কেউ এই লিংকে গিয়ে দেখতে পাবে (যদিও দ্বিতীয়বার অ্যাকাউন্ট বানাতে পারবে না)।</p>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <?php if ($existing === 0 && !$success): ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>ইউজারনেম</label>
        <input type="text" name="username" required>
        <label>পাসওয়ার্ড (কমপক্ষে ৮ অক্ষর)</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn">অ্যাডমিন তৈরি করো</button>
    </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
