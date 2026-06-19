<?php
// এই ফাইল ব্যবহারের আগে অবশ্যই includes/locations.php require করে $locations ভ্যারিয়েবল লোড করে নিতে হবে।
// ব্যবহারের আগে এই ভ্যারিয়েবলগুলো (ঐচ্ছিক) সেট করে নাও:
//   $loc_division, $loc_district, $loc_upazila  -> আগে থেকে সিলেক্ট করা মান (এডিট ফর্মে কাজে লাগে)
//   $loc_required (default true), $loc_show_upazila (default true)
//   $loc_id_prefix (default '') -> একই পেজে একাধিকবার বসালে আইডি কনফ্লিক্ট এড়াতে আলাদা prefix দাও

$loc_division = $loc_division ?? '';
$loc_district = $loc_district ?? '';
$loc_upazila = $loc_upazila ?? '';
$loc_required = $loc_required ?? true;
$loc_show_upazila = $loc_show_upazila ?? true;
$loc_id_prefix = $loc_id_prefix ?? '';
$req = $loc_required ? 'required' : '';
?>
<div class="row2">
    <div>
        <label>বিভাগ</label>
        <select name="division" id="<?php echo $loc_id_prefix; ?>division" <?php echo $req; ?>>
            <option value="">সিলেক্ট করুন</option>
            <?php foreach (array_keys($locations) as $div): ?>
                <option value="<?php echo $div; ?>" <?php echo $loc_division === $div ? 'selected' : ''; ?>><?php echo $div; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label>জেলা</label>
        <select name="district" id="<?php echo $loc_id_prefix; ?>district" <?php echo $req; ?>>
            <option value="">আগে বিভাগ সিলেক্ট করুন</option>
        </select>
    </div>
</div>
<?php if ($loc_show_upazila): ?>
<label>উপজেলা</label>
<select name="upazila" id="<?php echo $loc_id_prefix; ?>upazila">
    <option value="">আগে জেলা সিলেক্ট করুন</option>
</select>
<?php endif; ?>

<script>
(function() {
    const locData = <?php echo json_encode($locations, JSON_UNESCAPED_UNICODE); ?>;
    const presetDistrict = <?php echo json_encode($loc_district, JSON_UNESCAPED_UNICODE); ?>;
    const presetUpazila = <?php echo json_encode($loc_upazila, JSON_UNESCAPED_UNICODE); ?>;
    const showUpazila = <?php echo $loc_show_upazila ? 'true' : 'false'; ?>;

    const divisionEl = document.getElementById('<?php echo $loc_id_prefix; ?>division');
    const districtEl = document.getElementById('<?php echo $loc_id_prefix; ?>district');
    const upazilaEl = showUpazila ? document.getElementById('<?php echo $loc_id_prefix; ?>upazila') : null;

    function fillDistricts(selectedDivision, selectedDistrict) {
        districtEl.innerHTML = '<option value="">সিলেক্ট করুন</option>';
        if (!selectedDivision || !locData[selectedDivision]) return;
        Object.keys(locData[selectedDivision]).forEach(function(d) {
            const opt = document.createElement('option');
            opt.value = d;
            opt.textContent = d;
            if (d === selectedDistrict) opt.selected = true;
            districtEl.appendChild(opt);
        });
    }

    function fillUpazilas(selectedDivision, selectedDistrict, selectedUpazila) {
        if (!showUpazila) return;
        upazilaEl.innerHTML = '<option value="">সিলেক্ট করুন (ঐচ্ছিক)</option>';
        if (!selectedDivision || !selectedDistrict || !locData[selectedDivision] || !locData[selectedDivision][selectedDistrict]) return;
        locData[selectedDivision][selectedDistrict].forEach(function(u) {
            const opt = document.createElement('option');
            opt.value = u;
            opt.textContent = u;
            if (u === selectedUpazila) opt.selected = true;
            upazilaEl.appendChild(opt);
        });
    }

    divisionEl.addEventListener('change', function() {
        fillDistricts(this.value, '');
        if (showUpazila) fillUpazilas('', '', '');
    });

    districtEl.addEventListener('change', function() {
        if (showUpazila) fillUpazilas(divisionEl.value, this.value, '');
    });

    // পেজ লোড হওয়ার সময় আগে থেকে সিলেক্ট করা মান থাকলে (এডিট ফর্ম) সেগুলো বসিয়ে দাও
    if (divisionEl.value) {
        fillDistricts(divisionEl.value, presetDistrict);
        if (showUpazila) fillUpazilas(divisionEl.value, presetDistrict, presetUpazila);
    }
})();
</script>
