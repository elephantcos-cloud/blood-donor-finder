<?php
require __DIR__ . '/config.php';
require __DIR__ . '/includes/icons.php';
require __DIR__ . '/includes/lang.php';
require __DIR__ . '/includes/locations.php';
require __DIR__ . '/includes/avatar.php';
require __DIR__ . '/includes/donor_card.php';

$page_title = t('search_title') . ' - ' . t('site_name');

$f_blood_group = $_GET['blood_group'] ?? '';
$f_division = $_GET['division'] ?? '';
$f_district = $_GET['district'] ?? '';
$f_upazila = $_GET['upazila'] ?? '';
$f_sort = $_GET['sort'] ?? 'available';
if (!in_array($f_sort, ['available', 'newest', 'most'], true)) $f_sort = 'available';

// ---------- ডায়নামিক কোয়েরি — যেকোনো ফিল্টার ঐচ্ছিক ----------
$where = ['is_blocked = 0'];
$params = [];
$types = '';

if ($f_blood_group !== '') { $where[] = 'blood_group = ?'; $params[] = $f_blood_group; $types .= 's'; }
if ($f_division !== '')   { $where[] = 'division = ?';     $params[] = $f_division;   $types .= 's'; }
if ($f_district !== '')   { $where[] = 'district = ?';     $params[] = $f_district;   $types .= 's'; }
if ($f_upazila !== '')    { $where[] = 'upazila = ?';      $params[] = $f_upazila;    $types .= 's'; }

$order_by = match ($f_sort) {
    'newest' => 'created_at DESC',
    'most' => 'total_donations DESC',
    default => '(last_donation_date IS NULL OR DATEDIFF(CURDATE(), last_donation_date) >= 120) DESC, name ASC',
};

$sql = "SELECT * FROM donors WHERE " . implode(' AND ', $where) . " ORDER BY $order_by LIMIT 60";
$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$results = $stmt->get_result();
$result_count = $results->num_rows;

require __DIR__ . '/includes/header.php';
?>

<h2 style="display:flex; align-items:center; gap:8px; margin-bottom:2px;"><?php echo icon('search', 'ic-md'); ?> <?php echo htmlspecialchars(t('search_title')); ?></h2>
<p class="muted" style="margin-top:0;"><?php echo htmlspecialchars(t('search_sub')); ?></p>

<div class="card">
    <form method="get" id="searchForm">
        <input type="hidden" id="bloodGroupInput" name="blood_group" value="<?php echo htmlspecialchars($f_blood_group); ?>">

        <label><?php echo icon('droplet', 'ic-xs'); ?> <?php echo htmlspecialchars(t('quick_bg_title')); ?></label>
        <div class="pill-row" style="margin-bottom:16px;">
            <button type="submit" name="blood_group" value="" class="pill-btn <?php echo $f_blood_group === '' ? 'active' : ''; ?>"><?php echo htmlspecialchars(t('filter_all')); ?></button>
            <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                <button type="submit" name="blood_group" value="<?php echo $bg; ?>" class="pill-btn <?php echo $f_blood_group === $bg ? 'active' : ''; ?>"><?php echo $bg; ?></button>
            <?php endforeach; ?>
        </div>

        <?php
        $loc_division = $f_division;
        $loc_district = $f_district;
        $loc_upazila = $f_upazila;
        $loc_required = false;
        require __DIR__ . '/includes/location_select.php';
        ?>

        <button type="submit" class="btn" style="margin-top:14px;"><?php echo icon('search', 'ic-sm'); ?> <?php echo htmlspecialchars(t('btn_find_donor')); ?></button>

        <div style="margin-top:18px; padding-top:14px; border-top:1px solid var(--border);">
            <label style="margin-bottom:8px;"><?php echo icon('filter', 'ic-xs'); ?> <?php echo htmlspecialchars(t('sort_by')); ?></label>
            <div class="pill-row">
                <button type="submit" name="sort" value="available" class="pill-btn <?php echo $f_sort === 'available' ? 'active' : ''; ?>"><?php echo htmlspecialchars(t('sort_available')); ?></button>
                <button type="submit" name="sort" value="newest" class="pill-btn <?php echo $f_sort === 'newest' ? 'active' : ''; ?>"><?php echo htmlspecialchars(t('sort_newest')); ?></button>
                <button type="submit" name="sort" value="most" class="pill-btn <?php echo $f_sort === 'most' ? 'active' : ''; ?>"><?php echo htmlspecialchars(t('sort_most_donations')); ?></button>
            </div>
        </div>
    </form>
</div>

<p class="muted" style="font-weight:700; margin-bottom:10px;"><?php echo $result_count; ?> <?php echo htmlspecialchars(t('results_found')); ?></p>

<?php if ($result_count === 0): ?>
    <div class="card" style="text-align:center; color:var(--muted);">
        <?php echo icon('search', 'ic-lg'); ?>
        <p><?php echo htmlspecialchars(t('no_results')); ?></p>
    </div>
<?php else: ?>
    <div class="donor-grid">
        <?php while ($d = $results->fetch_assoc()): ?>
            <?php echo render_donor_card($d); ?>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
