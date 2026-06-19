    </div>

    <div class="footer">
        <div class="footer-links">
            <a href="/index.php"><?php echo htmlspecialchars(t('nav_home')); ?></a> &middot;
            <a href="/search.php"><?php echo htmlspecialchars(t('nav_search')); ?></a> &middot;
            <a href="/emergency.php"><?php echo htmlspecialchars(t('nav_emergency')); ?></a> &middot;
            <a href="/hospitals.php"><?php echo htmlspecialchars(t('nav_hospitals')); ?></a> &middot;
            <a href="/ambulances.php"><?php echo htmlspecialchars(t('nav_ambulances')); ?></a> &middot;
            <a href="/admin/login.php"><?php echo htmlspecialchars(t('nav_admin')); ?></a>
        </div>
        <?php echo htmlspecialchars(t('footer_tagline')); ?> &middot; &copy; <?php echo date('Y'); ?>
    </div>

    <button type="button" class="share-fab" onclick="shareSite()" aria-label="share"><?php echo icon('share', 'ic-sm'); ?></button>

    <div class="bottom-nav">
        <a href="/index.php" class="<?php echo $__current_page === 'index.php' ? 'active' : ''; ?>"><?php echo icon('home', 'ic'); ?><?php echo htmlspecialchars(t('nav_home')); ?></a>
        <a href="/search.php" class="<?php echo $__current_page === 'search.php' ? 'active' : ''; ?>"><?php echo icon('search', 'ic'); ?><?php echo htmlspecialchars(t('nav_search')); ?></a>
        <a href="/emergency.php" class="<?php echo $__current_page === 'emergency.php' ? 'active' : ''; ?>"><?php echo icon('siren', 'ic'); ?><?php echo htmlspecialchars(t('nav_emergency')); ?></a>
        <a href="<?php echo isset($_SESSION['donor_id']) ? '/dashboard.php' : '/login.php'; ?>" class="<?php echo in_array($__current_page, ['dashboard.php','login.php'], true) ? 'active' : ''; ?>"><?php echo icon('user', 'ic'); ?><?php echo htmlspecialchars(isset($_SESSION['donor_id']) ? t('nav_profile') : t('nav_login')); ?></a>
    </div>

    <?php require __DIR__ . '/compat_modal.php'; ?>

    <script src="/assets/app.js"></script>
</body>
</html>
