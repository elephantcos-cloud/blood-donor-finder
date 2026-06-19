<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'রক্তবন্ধন'; ?></title>
    <meta name="description" content="বাংলাদেশের সবচেয়ে নির্ভরযোগ্য রক্তদাতা খোঁজার প্ল্যাটফর্ম">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="topbar">
        <a href="/index.php" class="brand"><span class="drop"></span> রক্তবন্ধন</a>
        <div class="nav">
            <a href="/emergency.php" class="nav-emergency">জরুরি রক্ত</a>
            <a href="/hospitals.php">হাসপাতাল</a>
            <a href="/ambulances.php">অ্যাম্বুলেন্স</a>
            <?php if (isset($_SESSION['donor_id'])): ?>
                <a href="/dashboard.php">প্রোফাইল</a>
                <a href="/logout.php">লগ-আউট</a>
            <?php else: ?>
                <a href="/login.php">লগইন</a>
                <a href="/register.php">রেজিস্ট্রেশন</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container">
