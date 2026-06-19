<?php
require __DIR__ . '/config.php';

$page_title = "ভেরিফিকেশন - রক্তবন্ধন";
$public_id = $_GET['id'] ?? '';
$donor = null;

if ($public_id !== '') {
    $stmt = $conn->prepare("SELECT name, blood_group, district, is_verified, is_blocked, total_donations, last_donation_date FROM donors WHERE public_id = ?");
    $stmt->bind_param("s", $public_id);
    $stmt->execute();
    $donor = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

require __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <h1>ব্লাড কার্ড ভেরিফিকেশন</h1>
</div>

<div class="card" style="max-width:420px; margin:0 auto; text-align:center;">
    <?php if (!$donor): ?>
        <div class="alert alert-error">এই আইডি দিয়ে কোনো রক্তবন্ধু খুঁজে পাওয়া যায়নি। কার্ডটি সঠিক নাও হতে পারে।</div>
    <?php elseif ((int)$donor['is_blocked'] === 1): ?>
        <div class="alert alert-error">এই অ্যাকাউন্টটি বর্তমানে নিষ্ক্রিয়।</div>
    <?php else: ?>
        <div class="alert alert-success">✓ এটি একটি বৈধ রক্তবন্ধন কার্ড</div>
        <h3><?php echo htmlspecialchars($donor['name']); ?></h3>
        <div class="bg-badge" style="font-size:18px;"><?php echo htmlspecialchars($donor['blood_group']); ?></div>
        <p class="muted" style="margin-top:12px;">অবস্থান: <?php echo htmlspecialchars($donor['district']); ?></p>
        <p class="muted">মোট রক্তদান: <?php echo (int)$donor['total_donations']; ?> বার</p>
        <p class="muted">বর্তমান অবস্থা: <?php echo is_donor_available($donor['last_donation_date']) ? 'উপলব্ধ ✓' : 'বিরতিতে আছেন'; ?></p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
