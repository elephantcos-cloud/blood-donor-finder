<?php
/**
 * কাস্টম SVG আইকন লাইব্রেরি — emoji বা Font Awesome এর বদলে।
 * ব্যবহার: <?php echo icon('home', 'ic'); ?>
 * stroke="currentColor" ব্যবহার করা হয়েছে, তাই CSS color দিয়ে রং নিয়ন্ত্রণ করা যায়।
 */
function icon(string $name, string $class = 'ic'): string {
    $body = '';
    switch ($name) {
        case 'droplet':
            $body = '<path d="M12 2.5C9 7 5.5 11.5 5.5 15.5a6.5 6.5 0 0013 0C18.5 11.5 15 7 12 2.5z"/>';
            break;
        case 'home':
            $body = '<path d="M3.5 10.5 12 4l8.5 6.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M9.5 20v-6h5v6"/>';
            break;
        case 'search':
            $body = '<circle cx="10.5" cy="10.5" r="6.5"/><path d="M19.5 19.5l-4.3-4.3"/>';
            break;
        case 'siren':
            $body = '<path d="M5 13a7 7 0 0114 0v6H5z"/><path d="M12 3v3"/><path d="M5.5 8 4 6.5"/><path d="M18.5 8 20 6.5"/><path d="M3 21h18"/>';
            break;
        case 'user':
            $body = '<circle cx="12" cy="8" r="3.5"/><path d="M4.5 20c1.3-4 4-6 7.5-6s6.2 2 7.5 6"/>';
            break;
        case 'users':
            $body = '<circle cx="9" cy="8" r="3"/><path d="M3 19c.8-3.2 3-5 6-5s5.2 1.8 6 5"/><circle cx="17" cy="9" r="2.4"/><path d="M15.7 14.2c2 .3 3.5 1.8 4.1 4.3"/>';
            break;
        case 'hospital':
            $body = '<rect x="4" y="6" width="16" height="14" rx="1.5"/><path d="M9 6V4.5a1 1 0 011-1h4a1 1 0 011 1V6"/><path d="M12 10v6M9 13h6"/>';
            break;
        case 'phone':
            $body = '<path d="M5.5 4h3l1.5 4-2 1.5a11 11 0 005.5 5.5l1.5-2 4 1.5v3a1.5 1.5 0 01-1.6 1.5C10.8 18.7 5.3 13.2 4 6.1A1.5 1.5 0 015.5 4z"/>';
            break;
        case 'chat':
            $body = '<path d="M4 5.5h16v10H9l-4 3.5v-3.5H4z"/><path d="M8 10h8M8 13h5"/>';
            break;
        case 'pin':
            $body = '<path d="M12 21s7-6.5 7-12a7 7 0 10-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="9" r="2.3"/>';
            break;
        case 'calendar':
            $body = '<rect x="4" y="5.5" width="16" height="14.5" rx="1.5"/><path d="M4 9.5h16M8 3.5v4M16 3.5v4"/>';
            break;
        case 'check-circle':
            $body = '<circle cx="12" cy="12" r="8.5"/><path d="M8.2 12.3l2.5 2.5 5-5.2"/>';
            break;
        case 'x-circle':
            $body = '<circle cx="12" cy="12" r="8.5"/><path d="M9 9l6 6M15 9l-6 6"/>';
            break;
        case 'moon':
            $body = '<path d="M19 13.5A7.5 7.5 0 1110.5 5 6 6 0 0019 13.5z"/>';
            break;
        case 'sun':
            $body = '<circle cx="12" cy="12" r="4"/><path d="M12 3v2.2M12 18.8V21M4.6 4.6l1.6 1.6M17.8 17.8l1.6 1.6M3 12h2.2M18.8 12H21M4.6 19.4l1.6-1.6M17.8 6.2l1.6-1.6"/>';
            break;
        case 'share':
            $body = '<circle cx="18" cy="6" r="2.2"/><circle cx="6" cy="12" r="2.2"/><circle cx="18" cy="18" r="2.2"/><path d="M8 10.8l8-3.4M8 13.2l8 3.4"/>';
            break;
        case 'bell':
            $body = '<path d="M6 10.5a6 6 0 0112 0c0 4 1.5 5.5 1.5 5.5h-15S6 14.5 6 10.5z"/><path d="M10 19a2 2 0 004 0"/>';
            break;
        case 'arrow-right':
            $body = '<path d="M4.5 12h15M13.5 6l6 6-6 6"/>';
            break;
        case 'lock':
            $body = '<rect x="5.5" y="11" width="13" height="9" rx="1.5"/><path d="M8.5 11V8a3.5 3.5 0 017 0v3"/>';
            break;
        case 'close':
            $body = '<path d="M6 6l12 12M18 6L6 18"/>';
            break;
        case 'edit':
            $body = '<path d="M5 19l1-4 10.5-10.5a2 2 0 012.8 0l.2.2a2 2 0 010 2.8L9 18l-4 1z"/>';
            break;
        case 'trash':
            $body = '<path d="M5.5 7h13M9.5 7V5a1.5 1.5 0 011.5-1.5h2A1.5 1.5 0 0114.5 5v2"/><path d="M7 7l1 12.5a1.5 1.5 0 001.5 1.5h5a1.5 1.5 0 001.5-1.5L17 7"/>';
            break;
        case 'plus':
            $body = '<path d="M12 5v14M5 12h14"/>';
            break;
        case 'filter':
            $body = '<path d="M4 5.5h16L14 13v6l-4 2v-8z"/>';
            break;
        case 'chart':
            $body = '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 14h16M14 4v16"/>';
            break;
        case 'shield':
            $body = '<path d="M12 3.5l7 2.5v5.5c0 5-3 8-7 9.5-4-1.5-7-4.5-7-9.5V6z"/><path d="M8.5 12l2.3 2.3L15.5 9.5"/>';
            break;
        case 'camera':
            $body = '<rect x="3.5" y="7.5" width="17" height="12" rx="2"/><circle cx="12" cy="13.5" r="3.4"/><path d="M8.5 7.5l1.2-2h4.6l1.2 2"/>';
            break;
        case 'clock':
            $body = '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>';
            break;
        case 'map':
            $body = '<path d="M9 4.5L4 6.5v13l5-2 6 2 5-2v-13l-5 2-6-2z"/><path d="M9 4.5v13M15 6.5v13"/>';
            break;
        case 'globe':
            $body = '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.5 2.3 3.8 5.3 3.8 8.5s-1.3 6.2-3.8 8.5c-2.5-2.3-3.8-5.3-3.8-8.5s1.3-6.2 3.8-8.5z"/>';
            break;
        case 'logout':
            $body = '<path d="M9 4.5H6A1.5 1.5 0 004.5 6v12A1.5 1.5 0 006 19.5h3"/><path d="M14.5 8.5L19 12l-4.5 3.5M19 12H9.5"/>';
            break;
        case 'drop-fill':
            $body = '<path d="M12 2.5C9 7 5.5 11.5 5.5 15.5a6.5 6.5 0 0013 0C18.5 11.5 15 7 12 2.5z" fill="currentColor" stroke="none"/>';
            break;
        default:
            $body = '<circle cx="12" cy="12" r="8.5"/>';
    }
    return '<svg class="' . htmlspecialchars($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
}
