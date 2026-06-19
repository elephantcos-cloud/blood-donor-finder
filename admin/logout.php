<?php
require __DIR__ . '/../config.php';
unset($_SESSION['admin_id'], $_SESSION['admin_username']);
redirect('/admin/login.php');
