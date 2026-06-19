<?php
require __DIR__ . '/config.php';
session_destroy();
header("Location: /index.php");
exit;
