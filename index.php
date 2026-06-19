<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/icons.php';
require __DIR__ . '/includes/lang.php';
require __DIR__ . '/includes/avatar.php';
require __DIR__ . '/includes/donor_card.php';

$page_title = t('site_name') . ' - ' . t('hero_title_1') . ' ' . t('hero_title_2');

$total_donors = $conn->query("SELECT COUNT(*) c FROM donors WHERE is_blocked=0")->fetch_assoc()['c'];
$available_donors = $conn->query("SELECT COUNT(*) c FROM donors WHERE is_blocked=0 AND (last_donation_date IS NULL OR DATEDIFF(CURDATE(), last_donation_date) >= 120)")->fetch_assoc()['c'];
$active_requests = $conn->query("SELECT COUNT(*) c FROM emergency_requests WHERE status='active'")->fetch_assoc()['c'];
$district_coverage = $conn->query("SELECT COUNT(DISTINCT district) c FROM donors")->fetch_assoc()['c'];

$recent_donors = $conn->query("SELECT * FROM donors WHERE is_blocked=0 ORDER BY created_at DESC LIMIT 6");

$blood_groups_display = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

require __DIR__ . '/includes/header.php';
?>

<div class="hero-banner">
    <div class="hero-banner-inner">
        <div class="hero-pill"><?php echo icon('droplet', 'ic-sm'); ?> <?php echo htmlspecialchars(t('hero_badge')); ?></div>
        <h1><?php echo htmlspecialchars(t('hero_title_1')); ?><br><?php echo htmlspecialchars(t('hero_title_2')); ?></h1>
        <p><?php echo htmlspecialchars(t('hero_sub')); ?></p>
        <div class="hero-actions-row">
            <a href="/search.php" class="hero-btn-light"><?php echo icon('search', 'ic-sm'); ?><?php echo htmlspecialchars(t('btn_find_donor')); ?></a>
            <a href="/emergency.php?new=1" class="hero-btn-ghost"><?php echo icon('siren', 'ic-sm'); ?><?php echo htmlspecialchars(t('btn_emergency')); ?></a>
            <button type="button" onclick="openModal('compatModal')" class="hero-btn-ghost" style="border:none; cursor:pointer; font-family:inherit;"><?php echo icon('chart', 'ic-sm'); ?><?php echo htmlspecialchars(t('btn_compat_chart')); ?></button>
        </div>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-icon-circle"><?php echo icon('users', 'ic'); ?></div><div class="stat-num"><?php echo $total_donors; ?></div><div class="stat-label"><?php echo htmlspecialchars(t('stat_total_donors')); ?></div></div>
    <div class="stat-card"><div class="stat-icon-circle"><?php echo icon('check-circle', 'ic'); ?></div><div class="stat-num"><?php echo $available_donors; ?></div><div class="stat-label"><?php echo htmlspecialchars(t('stat_available')); ?></div></div>
    <div class="stat-card"><div class="stat-icon-circle"><?php echo icon('siren', 'ic'); ?></div><div class="stat-num"><?php echo $active_requests; ?></div><div class="stat-label"><?php echo htmlspecialchars(t('stat_active_requests')); ?></div></div>
    <div class="stat-card"><div class="stat-icon-circle"><?php echo icon('map', 'ic'); ?></div><div class="stat-num"><?php echo $district_coverage; ?></div><div class="stat-label"><?php echo htmlspecialchars(t('stat_district_coverage')); ?></div></div>
</div>

<div class="card">
    <h3 style="margin-top:0;"><?php echo icon('droplet', 'ic-sm'); ?> <?php echo htmlspecialchars(t('quick_bg_title')); ?></h3>
    <p class="muted" style="margin-top:-6px;"><?php echo htmlspecialchars(t('quick_bg_sub')); ?></p>
    <div class="quick-bg-grid">
        <?php foreach ($blood_groups_display as $bg): ?>
            <a href="/search.php?blood_group=<?php echo urlencode($bg); ?>" class="quick-bg-btn" style="color:<?php echo blood_group_color($bg); ?>; border-color:<?php echo blood_group_color($bg); ?>33;">
                <?php echo $bg; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
        <h3 style="margin:0;"><?php echo icon('users', 'ic-sm'); ?> <?php echo htmlspecialchars(t('recent_donors')); ?></h3>
        <a href="/search.php" style="font-size:13px; font-weight:700; display:flex; align-items:center; gap:3px;"><?php echo htmlspecialchars(t('view_all')); ?> <?php echo icon('arrow-right', 'ic-xs'); ?></a>
    </div>
    <?php if ($recent_donors->num_rows === 0): ?>
        <p class="muted"><?php echo htmlspecialchars(t('no_donors_yet')); ?></p>
    <?php else: ?>
        <div class="donor-grid" style="margin-top:12px;">
            <?php while ($d = $recent_donors->fetch_assoc()): ?>
                <?php echo render_donor_card($d); ?>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
