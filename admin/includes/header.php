<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'অ্যাডমিন প্যানেল'; ?> - রক্তবন্ধন</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="topbar">
        <a href="/admin/dashboard.php" class="brand"><span class="drop"></span> অ্যাডমিন প্যানেল</a>
        <div class="nav">
            <a href="/index.php" target="_blank">সাইট দেখো</a>
            <a href="/admin/logout.php">লগ-আউট</a>
        </div>
    </div>
    <div class="container">
        <div class="tabs">
            <a href="/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">ড্যাশবোর্ড</a>
            <a href="/admin/donors.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'donors.php' ? 'active' : ''; ?>">ডোনার ম্যানেজমেন্ট</a>
            <a href="/admin/requests.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'requests.php' ? 'active' : ''; ?>">জরুরি রিকোয়েস্ট</a>
            <a href="/admin/hospitals.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'hospitals.php' ? 'active' : ''; ?>">হাসপাতাল</a>
            <a href="/admin/ambulances.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'ambulances.php' ? 'active' : ''; ?>">অ্যাম্বুলেন্স</a>
        </div>
