<?php
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/lang.php';
$__current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="<?php echo $CURRENT_LANG; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? t('site_name'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(t('hero_sub')); ?>">
    <link rel="stylesheet" href="/assets/style.css?v=<?php echo @filemtime(__DIR__ . '/../assets/style.css') ?: time(); ?>">
    <script>
        if (localStorage.getItem('rb-theme') === 'dark') document.documentElement.classList.add('dark');
    </script>
</head>
<body>
    <div class="topbar">
        <a href="/index.php" class="brand"><?php echo icon('drop-fill', 'ic-lg'); ?> <?php echo htmlspecialchars(t('site_name')); ?></a>
        <div class="nav">
            <a href="/index.php"><?php echo icon('home', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_home')); ?></a>
            <a href="/search.php"><?php echo icon('search', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_search')); ?></a>
            <a href="/emergency.php" class="nav-emergency"><?php echo icon('siren', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_emergency')); ?></a>
            <a href="/hospitals.php"><?php echo icon('hospital', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_hospitals')); ?></a>
            <a href="/ambulances.php"><?php echo icon('siren', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_ambulances')); ?></a>
            <?php if (isset($_SESSION['donor_id'])): ?>
                <a href="/dashboard.php"><?php echo icon('user', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_profile')); ?></a>
                <a href="/logout.php"><?php echo icon('logout', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_logout')); ?></a>
            <?php else: ?>
                <a href="/login.php"><?php echo icon('lock', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_login')); ?></a>
                <a href="/register.php"><?php echo icon('plus', 'ic-sm'); ?><?php echo htmlspecialchars(t('nav_register')); ?></a>
            <?php endif; ?>
            <a href="<?php echo htmlspecialchars(lang_switch_url($CURRENT_LANG === 'bn' ? 'en' : 'bn')); ?>" class="lang-toggle"><?php echo icon('globe', 'ic-xs'); ?><?php echo htmlspecialchars(t('lang_switch')); ?></a>
            <button type="button" class="icon-btn" onclick="toggleTheme()" aria-label="theme">
                <span class="icon-moon"><?php echo icon('moon', 'ic-sm'); ?></span>
                <span class="icon-sun"><?php echo icon('sun', 'ic-sm'); ?></span>
            </button>
        </div>
    </div>
    <div class="container">
