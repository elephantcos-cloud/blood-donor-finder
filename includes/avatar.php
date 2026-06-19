<?php
/**
 * ডোনারের অ্যাভাটার দেখানোর হেল্পার — ছবি থাকলে ছবি, না থাকলে নামের প্রথম অক্ষর দিয়ে রঙিন বৃত্ত।
 */

function blood_group_color(string $bg): string {
    $colors = [
        'A+' => '#dc2626', 'A-' => '#b91c1c',
        'B+' => '#c2410c', 'B-' => '#9a3412',
        'AB+' => '#7c3aed', 'AB-' => '#6d28d9',
        'O+' => '#16a34a', 'O-' => '#047857',
    ];
    return $colors[$bg] ?? '#C81D33';
}

function avatar_html(?string $photo_path, string $name, string $blood_group, int $size = 48): string {
    $color = blood_group_color($blood_group);
    if (!empty($photo_path)) {
        $src = '/' . ltrim($photo_path, '/');
        return '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($name) . '" class="avatar-img" style="width:' . $size . 'px;height:' . $size . 'px;border-color:' . $color . '">';
    }
    $initial = mb_substr(trim($name), 0, 1, 'UTF-8');
    if ($initial === '') $initial = '?';
    return '<div class="avatar-initials" style="width:' . $size . 'px;height:' . $size . 'px;background:' . $color . ';font-size:' . round($size * 0.42) . 'px;">' . htmlspecialchars($initial) . '</div>';
}
