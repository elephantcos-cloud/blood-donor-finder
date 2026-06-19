<?php
require __DIR__ . '/config.php';

if (!isset($_SESSION['donor_id'])) {
    redirect('/login.php');
}

$page_title = "আমার ব্লাড কার্ড - রক্তবন্ধন";
$stmt = $conn->prepare("SELECT * FROM donors WHERE id = ?");
$stmt->bind_param("i", $_SESSION['donor_id']);
$stmt->execute();
$donor = $stmt->get_result()->fetch_assoc();
$stmt->close();

$available = is_donor_available($donor['last_donation_date']);
$verify_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/verify.php?id=' . urlencode($donor['public_id']);
$qr_img = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verify_url);

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>আমার ব্লাড কার্ড</h1>
    <p class="muted">এই কার্ডের স্ক্রিনশট নিয়ে রাখো, প্রয়োজনে দেখাতে পারবে</p>
</div>

<div class="blood-card">
    <div class="bc-id">আইডি: <?php echo htmlspecialchars($donor['public_id']); ?></div>
    <h3><?php echo htmlspecialchars($donor['name']); ?></h3>
    <div class="bc-bg"><?php echo htmlspecialchars($donor['blood_group']); ?></div>
    <div class="bc-row"><span>অবস্থান</span><span><?php echo htmlspecialchars($donor['district']); ?></span></div>
    <div class="bc-row"><span>মোট রক্তদান</span><span><?php echo (int)$donor['total_donations']; ?> বার</span></div>
    <div class="bc-row"><span>স্ট্যাটাস</span><span><?php echo $available ? 'উপলব্ধ ✓' : 'বিরতিতে'; ?></span></div>
    <div class="bc-qr"><img src="<?php echo htmlspecialchars($qr_img); ?>" alt="QR Code" width="120" height="120"></div>
</div>

<div class="card" style="max-width:380px; margin:20px auto; text-align:center;">
    <p class="muted">QR কোড স্ক্যান করে যে কেউ এই কার্ডের সত্যতা যাচাই করতে পারবে।</p>
    <button onclick="window.print()" class="btn">কার্ড প্রিন্ট/PDF করো</button>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
