<?php
// অ্যাডমিন প্যানেলের প্রতিটা প্রোটেক্টেড পেজের শুরুতে এই ফাইল require করতে হবে।
// এটা ধরে নেয় যে config.php (যেটা session_start() করে) আগেই require করা হয়েছে।

if (!isset($_SESSION['admin_id'])) {
    header("Location: /admin/login.php");
    exit;
}
