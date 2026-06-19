<?php
require __DIR__ . '/config.php';

$page_title = "লগইন - রক্তবন্ধন";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = "ফর্ম সেশন মেয়াদোত্তীর্ণ হয়েছে, আবার চেষ্টা করুন।";
    } else {
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT id, name, password, is_blocked FROM donors WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $donor = $result->fetch_assoc();
            if ((int)$donor['is_blocked'] === 1) {
                $error = "তোমার অ্যাকাউন্ট সাময়িকভাবে স্থগিত করা হয়েছে। অ্যাডমিনের সাথে যোগাযোগ করো।";
            } elseif (password_verify($password, $donor['password'])) {
                $_SESSION['donor_id'] = $donor['id'];
                $_SESSION['donor_name'] = $donor['name'];
                redirect('/dashboard.php');
            } else {
                $error = "ভুল মোবাইল নম্বর বা পাসওয়ার্ড।";
            }
        } else {
            $error = "ভুল মোবাইল নম্বর বা পাসওয়ার্ড।";
        }
        $stmt->close();
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>লগইন করুন</h1>
</div>

<div class="card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <?php echo csrf_field(); ?>
        <label>মোবাইল নম্বর</label>
        <input type="text" name="phone" required>

        <label>পাসওয়ার্ড</label>
        <input type="password" name="password" required>

        <button type="submit" class="btn">লগইন</button>
    </form>
    <p class="muted" style="margin-top:14px;">নতুন? <a href="/register.php">রেজিস্ট্রেশন করুন</a></p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
