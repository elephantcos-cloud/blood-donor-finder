<?php
/**
 * ভাষা সিস্টেম — UI-এর মূল অংশ (নেভিগেশন, বাটন, হেডিং, লেবেল) বাংলা ও ইংরেজি দুই ভাষায় দেখানোর জন্য।
 * ডোনাররা নিজেরা যা টাইপ করে (নাম, ঠিকানা ইত্যাদি) সেটা যেই ভাষায় লেখা হয়েছে সেভাবেই থাকবে — এটা অনুবাদ হয় না।
 * এই ফাইল config.php (session_start করা) এর পরে require করতে হবে।
 */

if (isset($_GET['lang']) && in_array($_GET['lang'], ['bn', 'en'], true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
$CURRENT_LANG = $_SESSION['lang'] ?? 'bn';

$TRANSLATIONS = [
    'bn' => [
        'site_name' => 'রক্তবন্ধন',
        'nav_home' => 'হোম',
        'nav_search' => 'দাতা খুঁজুন',
        'nav_emergency' => 'জরুরি রক্ত',
        'nav_hospitals' => 'হাসপাতাল',
        'nav_ambulances' => 'অ্যাম্বুলেন্স',
        'nav_login' => 'লগইন',
        'nav_register' => 'রেজিস্ট্রেশন',
        'nav_profile' => 'প্রোফাইল',
        'nav_logout' => 'লগ-আউট',
        'nav_admin' => 'অ্যাডমিন',

        'hero_badge' => 'বাংলাদেশের স্মার্ট ব্লাড নেটওয়ার্ক',
        'hero_title_1' => 'আপনার এক ফোঁটা রক্ত',
        'hero_title_2' => 'বাঁচাতে পারে একটি জীবন',
        'hero_sub' => 'বিভাগ, জেলা ও উপজেলা দিয়ে নিকটস্থ রক্তবন্ধু খুঁজুন',
        'btn_find_donor' => 'রক্তদাতা খুঁজুন',
        'btn_emergency' => 'জরুরি রক্ত দরকার',
        'btn_become_donor' => 'রক্তবন্ধু হোন',
        'btn_compat_chart' => 'সামঞ্জস্য চার্ট',

        'stat_total_donors' => 'মোট রক্তবন্ধু',
        'stat_available' => 'এখন উপলব্ধ',
        'stat_active_requests' => 'সক্রিয় জরুরি রিকোয়েস্ট',
        'stat_district_coverage' => 'জেলা কভারেজ',

        'quick_bg_title' => 'রক্তের গ্রুপ',
        'quick_bg_sub' => 'ক্লিক করে সেই গ্রুপের দাতা দেখুন',

        'recent_donors' => 'সাম্প্রতিক রক্তবন্ধু',
        'view_all' => 'সব দেখুন',
        'no_donors_yet' => 'এখনো কোনো রক্তবন্ধু যুক্ত হয়নি।',

        'search_title' => 'দাতা খুঁজুন',
        'search_sub' => 'শুধু রক্তের গ্রুপ বা শুধু বিভাগ দিয়েও খোঁজা যাবে — যত বেশি ফিল্টার, তত নির্দিষ্ট ফলাফল',
        'filter_division' => 'বিভাগ',
        'filter_district' => 'জেলা',
        'filter_upazila' => 'উপজেলা',
        'filter_all' => 'সব',
        'sort_by' => 'সাজান',
        'sort_available' => 'উপলব্ধ আগে',
        'sort_newest' => 'নতুন',
        'sort_most_donations' => 'বেশি দান',
        'results_found' => 'জন রক্তবন্ধু পাওয়া গেছে',
        'no_results' => 'এই ফিল্টারে কোনো রক্তবন্ধু পাওয়া যায়নি। ফিল্টার কমিয়ে আবার চেষ্টা করো।',

        'status_available' => 'উপলব্ধ',
        'status_cooldown_suffix' => 'দিন পরে',
        'btn_call' => 'কল করুন',
        'btn_whatsapp' => 'WhatsApp',
        'female_contact_note' => 'নারী ডোনার — যোগাযোগের জন্য অ্যাডমিনের সাহায্য নিন',

        'compat_modal_title' => 'রক্তের সামঞ্জস্য চার্ট',
        'compat_modal_sub' => 'কোন গ্রুপ কাকে রক্ত দিতে পারবে',
        'compat_universal_donor' => 'O- সার্বজনীন দাতা',
        'compat_universal_recipient' => 'AB+ সার্বজনীন গ্রহীতা',

        'emg_badge' => 'জরুরি সেবা',
        'emg_title' => 'জরুরি রক্ত প্রয়োজন?',
        'emg_sub' => 'হাসপাতালে রোগীর জন্য রক্ত দরকার? এখানে রিকোয়েস্ট দিন।',
        'emg_post_btn' => 'নতুন রিকোয়েস্ট পোস্ট করো',
        'emg_active_title' => 'সক্রিয় রিকোয়েস্ট',
        'emg_none' => 'এখন কোনো সক্রিয় জরুরি রিকোয়েস্ট নেই।',
        'emg_bags' => 'ব্যাগ প্রয়োজন',

        'footer_tagline' => 'আমরা রক্তবন্ধন, রক্তের সম্পর্ক গড়ি',

        'lang_switch' => 'English',
    ],
    'en' => [
        'site_name' => 'RoktoBondhon',
        'nav_home' => 'Home',
        'nav_search' => 'Find Donors',
        'nav_emergency' => 'Emergency',
        'nav_hospitals' => 'Hospitals',
        'nav_ambulances' => 'Ambulance',
        'nav_login' => 'Login',
        'nav_register' => 'Register',
        'nav_profile' => 'Profile',
        'nav_logout' => 'Logout',
        'nav_admin' => 'Admin',

        'hero_badge' => "Bangladesh's Smart Blood Network",
        'hero_title_1' => 'One drop of your blood',
        'hero_title_2' => 'can save a life',
        'hero_sub' => 'Find nearby blood donors by division, district & upazila',
        'btn_find_donor' => 'Find Donors',
        'btn_emergency' => 'Need Blood Urgently',
        'btn_become_donor' => 'Become a Donor',
        'btn_compat_chart' => 'Compatibility Chart',

        'stat_total_donors' => 'Total Donors',
        'stat_available' => 'Available Now',
        'stat_active_requests' => 'Active Emergencies',
        'stat_district_coverage' => 'District Coverage',

        'quick_bg_title' => 'Blood Groups',
        'quick_bg_sub' => 'Click a group to see its donors',

        'recent_donors' => 'Recent Donors',
        'view_all' => 'View All',
        'no_donors_yet' => 'No donors have joined yet.',

        'search_title' => 'Find Donors',
        'search_sub' => 'Search with just a blood group, just a division, or any combination of filters',
        'filter_division' => 'Division',
        'filter_district' => 'District',
        'filter_upazila' => 'Upazila',
        'filter_all' => 'All',
        'sort_by' => 'Sort',
        'sort_available' => 'Available First',
        'sort_newest' => 'Newest',
        'sort_most_donations' => 'Most Donations',
        'results_found' => 'donors found',
        'no_results' => 'No donors matched these filters. Try removing a filter.',

        'status_available' => 'Available',
        'status_cooldown_suffix' => 'days left',
        'btn_call' => 'Call',
        'btn_whatsapp' => 'WhatsApp',
        'female_contact_note' => 'Female donor — contact via admin',

        'compat_modal_title' => 'Blood Compatibility Chart',
        'compat_modal_sub' => 'Which group can donate to whom',
        'compat_universal_donor' => 'O- universal donor',
        'compat_universal_recipient' => 'AB+ universal recipient',

        'emg_badge' => 'Emergency Service',
        'emg_title' => 'Need Blood Urgently?',
        'emg_sub' => 'A patient at a hospital needs blood? Post a request here.',
        'emg_post_btn' => 'Post a New Request',
        'emg_active_title' => 'Active Requests',
        'emg_none' => 'No active emergency requests right now.',
        'emg_bags' => 'bag(s) needed',

        'footer_tagline' => 'RoktoBondhon — building bonds of blood',

        'lang_switch' => 'বাংলা',
    ],
];

function t(string $key): string {
    global $TRANSLATIONS, $CURRENT_LANG;
    return $TRANSLATIONS[$CURRENT_LANG][$key] ?? ($TRANSLATIONS['bn'][$key] ?? $key);
}

// বর্তমান URL ঠিক রেখে শুধু ভাষা প্যারামিটার বদলানোর লিংক বানানোর হেল্পার
function lang_switch_url(string $lang): string {
    $path = strtok($_SERVER['REQUEST_URI'], '?');
    $q = $_GET;
    $q['lang'] = $lang;
    return $path . '?' . http_build_query($q);
}
