<?php
require __DIR__ . '/../config.php';

$page_title = "অ্যাডমিন লগইন";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = "ফর্ম সেশন মেয়াদোত্তীর্ণ, আবার চেষ্টা করো।";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $username;
                redirect('/admin/dashboard.php');
            } else {
                $error = "ভুল ইউজারনেম বা পাসওয়ার্ড।";
            }
        } else {
            $error = "ভুল ইউজারনেম বা পাসওয়ার্ড।";
        }
        $stmt->close();
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="card" style="max-width:380px; margin:30px auto;">
    <h3>অ্যাডমিন লগইন</h3>
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>ইউজারনেম</label>
        <input type="text" name="username" required>
        <label>পাসওয়ার্ড</label>
        <input type="password" name="password" required>
        <button type="submit" class="btn">লগইন</button>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
