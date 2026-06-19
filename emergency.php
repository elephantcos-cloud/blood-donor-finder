<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/locations.php';
require __DIR__ . '/includes/icons.php';
require __DIR__ . '/includes/lang.php';

$page_title = "জরুরি রক্ত - রক্তবন্ধন";
$success = '';
$error = '';
$show_form = isset($_GET['new']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = "ফর্ম সেশন মেয়াদোত্তীর্ণ হয়েছে, আবার চেষ্টা করুন।";
        $show_form = true;
    } else {
        $patient_name = trim($_POST['patient_name'] ?? '');
        $hospital_name = trim($_POST['hospital_name'] ?? '');
        $blood_group = $_POST['blood_group'] ?? '';
        $bags_needed = max(1, (int)($_POST['bags_needed'] ?? 1));
        $contact_number = trim($_POST['contact_number'] ?? '');
        $division = $_POST['division'] ?? '';
        $district = $_POST['district'] ?? '';
        $upazila = trim($_POST['upazila'] ?? '');
        $urgency_level = $_POST['urgency_level'] ?? 'normal';

        if (!in_array($urgency_level, ['normal', 'urgent', 'critical'], true)) {
            $urgency_level = 'normal';
        }

        if ($patient_name === '' || $hospital_name === '' || $blood_group === '' || $contact_number === '' || $division === '' || $district === '') {
            $error = "তারকা (*) চিহ্নিত সব ঘর পূরণ করুন।";
            $show_form = true;
        } elseif (!preg_match('/^01[0-9]{9}$/', $contact_number)) {
            $error = "একটি সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন।";
            $show_form = true;
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO emergency_requests (patient_name, hospital_name, blood_group, bags_needed, contact_number, division, district, upazila, urgency_level, status)
                 VALUES (?,?,?,?,?,?,?,?,?, 'active')"
            );
            $stmt->bind_param("sssisssss", $patient_name, $hospital_name, $blood_group, $bags_needed, $contact_number, $division, $district, $upazila, $urgency_level);
            if ($stmt->execute()) {
                $success = "রিকোয়েস্ট পোস্ট হয়েছে। দ্রুত সাড়া পেতে আশেপাশের রক্তবন্ধুদের সাথেও সরাসরি যোগাযোগ করো।";
                $show_form = false;
            } else {
                $error = "রিকোয়েস্ট পোস্ট করা যায়নি, আবার চেষ্টা করো।";
            }
            $stmt->close();
        }
    }
}

$requests = $conn->query(
    "SELECT * FROM emergency_requests WHERE status='active'
     ORDER BY FIELD(urgency_level,'critical','urgent','normal'), created_at DESC LIMIT 30"
);

require __DIR__ . '/includes/header.php';
?>

<div class="hero-banner" style="background: linear-gradient(135deg, #1a1014 0%, #2b171c 60%, #5c1a1a 100%);">
    <div class="hero-banner-inner">
        <div class="hero-pill"><?php echo icon('siren', 'ic-sm'); ?> <?php echo htmlspecialchars(t('emg_badge')); ?></div>
        <h1><?php echo htmlspecialchars(t('emg_title')); ?></h1>
        <p><?php echo htmlspecialchars(t('emg_sub')); ?></p>
        <div class="hero-actions-row">
            <a href="/emergency.php?new=1" class="hero-btn-light"><?php echo icon('plus', 'ic-sm'); ?><?php echo htmlspecialchars(t('emg_post_btn')); ?></a>
        </div>
    </div>
</div>

<?php if ($success): ?><div class="alert alert-success" style="max-width:680px;margin:0 auto 16px;"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

<?php if ($show_form): ?>
<div class="card">
    <?php if ($error): ?><div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>রোগীর নাম *</label>
        <input type="text" name="patient_name" required>

        <label>হাসপাতালের নাম *</label>
        <input type="text" name="hospital_name" required>

        <div class="row2">
            <div>
                <label>রক্তের গ্রুপ *</label>
                <select name="blood_group" required>
                    <option value="">সিলেক্ট করুন</option>
                    <?php foreach ($blood_groups as $bg): ?>
                        <option value="<?php echo $bg; ?>"><?php echo $bg; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>কত ব্যাগ লাগবে *</label>
                <input type="number" name="bags_needed" min="1" value="1" required>
            </div>
        </div>

        <label>যোগাযোগ নম্বর *</label>
        <input type="text" name="contact_number" placeholder="01XXXXXXXXX" required>

        <?php $loc_required = true; require __DIR__ . '/includes/location_select.php'; ?>

        <label>জরুরিতার মাত্রা *</label>
        <select name="urgency_level" required>
            <option value="normal">সাধারণ</option>
            <option value="urgent">জরুরি</option>
            <option value="critical">খুবই জরুরি (Critical)</option>
        </select>

        <button type="submit" class="btn">রিকোয়েস্ট পোস্ট করো</button>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <h3><?php echo icon('siren', 'ic-sm'); ?> <?php echo htmlspecialchars(t('emg_active_title')); ?> (<?php echo $requests->num_rows; ?>)</h3>
    <?php if ($requests->num_rows === 0): ?>
        <p class="muted"><?php echo htmlspecialchars(t('emg_none')); ?></p>
    <?php else: ?>
        <?php while ($r = $requests->fetch_assoc()):
            $wa_digits = preg_replace('/[^0-9]/', '', $r['contact_number']);
            if (substr($wa_digits, 0, 2) === '01') $wa_digits = '880' . substr($wa_digits, 1);
            $wa_text = rawurlencode('হ্যালো, রক্তবন্ধন থেকে দেখলাম ' . $r['hospital_name'] . ' হাসপাতালে ' . $r['blood_group'] . ' রক্ত দরকার। আমি সাহায্য করতে পারি।');
        ?>
            <div class="list-item">
                <h4><?php echo htmlspecialchars($r['patient_name']); ?> <span class="bg-badge"><?php echo $r['blood_group']; ?></span>
                    <span class="badge badge-<?php echo $r['urgency_level']; ?>"><?php echo ['normal'=>'সাধারণ','urgent'=>'জরুরি','critical'=>'খুবই জরুরি'][$r['urgency_level']]; ?></span>
                </h4>
                <div class="meta"><?php echo icon('hospital', 'ic-xs'); ?> <?php echo htmlspecialchars($r['hospital_name']); ?> &middot; <?php echo htmlspecialchars($r['district']); ?><?php echo $r['upazila'] ? ', ' . htmlspecialchars($r['upazila']) : ''; ?></div>
                <div class="meta"><?php echo (int)$r['bags_needed']; ?> <?php echo htmlspecialchars(t('emg_bags')); ?></div>
                <div class="dc-actions" style="margin-top:8px;">
                    <a href="tel:<?php echo htmlspecialchars($r['contact_number']); ?>" class="dc-btn dc-btn-call"><?php echo icon('phone', 'ic-sm'); ?><?php echo htmlspecialchars(t('btn_call')); ?></a>
                    <a href="https://wa.me/<?php echo $wa_digits; ?>?text=<?php echo $wa_text; ?>" target="_blank" class="dc-btn dc-btn-wa"><?php echo icon('chat', 'ic-sm'); ?><?php echo htmlspecialchars(t('btn_whatsapp')); ?></a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
