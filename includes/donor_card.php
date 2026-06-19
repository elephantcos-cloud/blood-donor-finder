<?php
/**
 * একটা ডোনার কার্ড রেন্ডার করার ফাংশন — হোমপেজ ও সার্চ পেজ দুটোতেই ব্যবহার হয়।
 * ব্যবহারের আগে অবশ্যই require করে নিতে হবে: config.php, icons.php, avatar.php, lang.php
 * $donor অ্যারেতে থাকতে হবে: name, photo_path, blood_group, district, upazila, gender, phone, whatsapp, last_donation_date
 */
function render_donor_card(array $donor): string {
    $available = is_donor_available($donor['last_donation_date'] ?? null);
    $days_left = days_until_eligible($donor['last_donation_date'] ?? null);
    $color = blood_group_color($donor['blood_group']);
    $progress_pct = $available ? 100 : max(4, round((1 - $days_left / 120) * 100));

    $status_html = $available
        ? '<span class="dc-status dc-status-ok">' . icon('check-circle', 'ic-sm') . t('status_available') . '</span>'
        : '<span class="dc-status dc-status-wait">' . icon('clock', 'ic-sm') . $days_left . ' ' . t('status_cooldown_suffix') . '</span>';

    $bar_html = '<div class="progress-wrap"><div class="progress-bar ' . ($available ? '' : 'cooldown') . '" style="width:' . $progress_pct . '%"></div></div>';

    $location = htmlspecialchars($donor['district'] ?? '');
    if (!empty($donor['upazila'])) {
        $location .= ', ' . htmlspecialchars($donor['upazila']);
    }

    $contact_html = '';
    if (($donor['gender'] ?? '') === 'female') {
        $contact_html = '<p class="female-note">' . t('female_contact_note') . '</p>';
    } else {
        $phone = htmlspecialchars($donor['phone'] ?? '');
        $contact_html = '<div class="dc-actions">';
        $contact_html .= '<a href="tel:' . $phone . '" class="dc-btn dc-btn-call">' . icon('phone', 'ic-sm') . t('btn_call') . '</a>';
        if (!empty($donor['whatsapp'])) {
            $wa_digits = preg_replace('/[^0-9]/', '', $donor['whatsapp']);
            if (substr($wa_digits, 0, 2) === '01') $wa_digits = '880' . substr($wa_digits, 1);
            $contact_html .= '<a href="https://wa.me/' . $wa_digits . '" target="_blank" class="dc-btn dc-btn-wa">' . icon('chat', 'ic-sm') . t('btn_whatsapp') . '</a>';
        }
        $contact_html .= '</div>';
    }

    $avatar = avatar_html($donor['photo_path'] ?? null, $donor['name'] ?? '', $donor['blood_group'], 52);

    return '<div class="donor-card">'
        . '<div class="dc-badge" style="background:' . $color . '">' . htmlspecialchars($donor['blood_group']) . '</div>'
        . '<div class="dc-top">' . $avatar
        . '<div class="dc-info"><div class="dc-name">' . htmlspecialchars($donor['name'] ?? '') . '</div>'
        . '<div class="dc-loc">' . icon('pin', 'ic-xs') . $location . '</div></div></div>'
        . $status_html
        . $bar_html
        . $contact_html
        . '</div>';
}
