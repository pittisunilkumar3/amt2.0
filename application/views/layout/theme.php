<?php
$theme = $this->customlib->getCurrentTheme();

if ($this->customlib->getRTL() != "") {
    if ($theme == "white") {
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css"/>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/white-rtl.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/AdminLTE-rtl.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/skins/_all-skins-rtl.min.css" />
        <?php
} else {
        ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/bootstrap-rtl/css/bootstrap-rtl.min.css"/>
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/AdminLTE-rtl.min.css" />
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/ss-rtlmain.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>backend/rtl/dist/css/skins/_all-skins-rtl.min.css" />
        <?php
}
}

if ($theme == "white") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/ss-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/dashboard.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/admin-pages.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} elseif ($theme == "default") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/default/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/default/ss-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} elseif ($theme == "red") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/red/skins/skin-red.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/red/ss-main-red.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} elseif ($theme == "blue") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/blue/skins/skin-darkblue.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/blue/ss-main-darkblue.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} elseif ($theme == "gray") {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/gray/skins/skin-light.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/gray/ss-main-light.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
} else {
    ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/style-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/default/skins/_all-skins.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/header-modern.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/default/ss-main.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/sidebar-treeview.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/vendor/remixicon/remixicon.css">
    <?php
}

// =========================================================================
// DYNAMIC CUSTOM THEME COLOUR - Each section is INDEPENDENT
// Only generates CSS for sections that have non-NULL values in the database
// =========================================================================

if (!function_exists('_tc_shift')) {
    function _tc_shift($hex, $amount) {
        $r = min(255, max(0, hexdec(substr($hex, 1, 2)) + $amount));
        $g = min(255, max(0, hexdec(substr($hex, 3, 2)) + $amount));
        $b = min(255, max(0, hexdec(substr($hex, 5, 2)) + $amount));
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                    . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                    . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
if (!function_exists('_tc_rgb')) {
    function _tc_rgb($hex) {
        return hexdec(substr($hex,1,2)) . ',' . hexdec(substr($hex,3,2)) . ',' . hexdec(substr($hex,5,2));
    }
}
if (!function_exists('_tc_text')) {
    function _tc_text($hex) {
        $r = hexdec(substr($hex,1,2));
        $g = hexdec(substr($hex,3,2));
        $b = hexdec(substr($hex,5,2));
        $lum = ($r * 299 + $g * 587 + $b * 114) / 1000;
        return ($lum > 160) ? 'dark' : 'light';
    }
}

// Read custom colours from session (with DB fallback)
$admin = $this->session->userdata('admin');
$tc_header = $tc_header_g = $tc_sidebar = $tc_sidebar_g = $tc_body_bg = '';
$tc_accent_start = $tc_accent_end = '';

if ($admin) {
    $tc_header    = isset($admin['theme_header_colour']) ? $admin['theme_header_colour'] : '';
    $tc_header_g  = isset($admin['theme_header_gradient']) ? $admin['theme_header_gradient'] : '';
    $tc_sidebar   = isset($admin['theme_sidebar_colour']) ? $admin['theme_sidebar_colour'] : '';
    $tc_sidebar_g = isset($admin['theme_sidebar_gradient']) ? $admin['theme_sidebar_gradient'] : '';
    $tc_body_bg   = isset($admin['theme_body_bg']) ? $admin['theme_body_bg'] : '';
    $tc_accent_start = isset($admin['theme_accent_start']) ? $admin['theme_accent_start'] : '';
    $tc_accent_end   = isset($admin['theme_accent_end']) ? $admin['theme_accent_end'] : '';

    if (!$tc_header && !$tc_sidebar && !$tc_body_bg && !$tc_accent_start) {
        $this->load->database();
        $this->load->model('setting_model');
        $sch = $this->setting_model->getSetting();
        if ($sch) {
            $tc_header    = $sch->theme_header_colour;
            $tc_header_g  = $sch->theme_header_gradient;
            $tc_sidebar   = $sch->theme_sidebar_colour;
            $tc_sidebar_g = $sch->theme_sidebar_gradient;
            $tc_body_bg   = $sch->theme_body_bg;
            $tc_accent_start = $sch->theme_accent_start;
            $tc_accent_end   = $sch->theme_accent_end;
            if ($admin) {
                $admin['theme_header_colour']    = $tc_header;
                $admin['theme_header_gradient']  = $tc_header_g;
                $admin['theme_sidebar_colour']   = $tc_sidebar;
                $admin['theme_sidebar_gradient'] = $tc_sidebar_g;
                $admin['theme_body_bg']          = $tc_body_bg;
                $admin['theme_accent_start']     = $tc_accent_start;
                $admin['theme_accent_end']       = $tc_accent_end;
                $this->session->set_userdata('admin', $admin);
            }
        }
    }
}

$has_header  = ($tc_header && $tc_header !== '');
$has_sidebar = ($tc_sidebar && $tc_sidebar !== '');
$has_body    = ($tc_body_bg && $tc_body_bg !== '');
$has_accent  = ($tc_accent_start && $tc_accent_start !== '');

if ($has_header || $has_sidebar || $has_body) {
    ?>
    <style>
    <?php

    // ===================== HEADER SECTION =====================
    if ($has_header) {
        $h1 = $tc_header;
        $h2 = $tc_header_g ? $tc_header_g : $h1;
        $h1_rgb = _tc_rgb($h1);
        $h_text = _tc_text($h1);
        $h_text_primary = ($h_text === 'light') ? '#ffffff' : '#1a1a1a';
        $h_text_secondary = ($h_text === 'light') ? 'rgba(255,255,255,0.85)' : 'rgba(0,0,0,0.7)';
        $h_text_muted = ($h_text === 'light') ? 'rgba(255,255,255,0.5)' : 'rgba(0,0,0,0.4)';
        $h_icon = ($h_text === 'light') ? 'rgba(255,255,255,0.85)' : 'rgba(0,0,0,0.6)';
        $h_icon_hover = ($h_text === 'light') ? '#ffffff' : '#000000';
        $header_bg_css = (strtolower($h1) === strtolower($h2))
            ? $h1
            : 'linear-gradient(90deg, ' . $h1 . ' 0%, ' . $h2 . ' 100%)';
        $header_accent = _tc_shift($h1, 30);
        $header_darker = _tc_shift($h1, -25);
        ?>
    /* ===== CUSTOM HEADER: <?php echo $h1; ?> ===== */
    :root {
        --header-primary: <?php echo $h1; ?>;
        --header-accent: <?php echo $header_accent; ?>;
        --header-bg: <?php echo $header_bg_css; ?>;
        --header-text-primary: <?php echo $h_text_primary; ?>;
        --header-text-secondary: <?php echo $h_text_secondary; ?>;
        --header-text-muted: <?php echo $h_text_muted; ?>;
        --header-icon-color: <?php echo $h_icon; ?>;
        --header-icon-hover: <?php echo $h_icon_hover; ?>;
        --search-focus-border: <?php echo $header_accent; ?>;
    }

    .main-header, .main-header > .navbar {
        background: <?php echo $header_bg_css; ?> !important;
        background-image: none !important;
    }
    .main-header .logo, .main-header .logo:hover {
        background: <?php echo $header_darker; ?> !important;
        background-image: none !important;
    }
    .main-header .logo span { color: <?php echo $h_text_primary; ?> !important; }

    .main-header .navbar .sidebar-toggle {
        color: <?php echo $h_icon; ?> !important;
    }
    .main-header .navbar .sidebar-toggle:hover {
        background-color: rgba(0,0,0,0.1) !important;
        color: <?php echo $h_icon_hover; ?> !important;
    }

    .main-header .navbar .nav > li > a {
        color: <?php echo $h_text_secondary; ?> !important;
    }
    .main-header .navbar .nav > li > a:hover,
    .main-header .navbar .nav > li > a:focus {
        background: rgba(0,0,0,0.1) !important;
        color: <?php echo $h_text_primary; ?> !important;
    }
    .main-header .navbar .nav .open > a,
    .main-header .navbar .nav .open > a:hover,
    .main-header .navbar .nav .open > a:focus {
        background: rgba(0,0,0,0.15) !important;
        color: <?php echo $h_text_primary; ?> !important;
    }

    .main-header .navbar .nav .user-menu > .dropdown-menu {
        border-top-color: <?php echo $h1; ?> !important;
    }
    .main-header .navbar .dropdown-menu > .active > a,
    .main-header .navbar .dropdown-menu > .active > a:hover,
    .main-header .navbar .dropdown-menu > .active > a:focus {
        background-color: <?php echo $h1; ?> !important;
        color: <?php echo $h_text_primary; ?> !important;
    }
    .main-header .navbar .dropdown-user > .dropdown-menu > li.user-header {
        background: <?php echo $header_bg_css; ?> !important;
    }
    .main-header .navbar .dropdown-user > .dropdown-menu > li.user-footer .btn-default:hover {
        background-color: <?php echo $header_darker; ?> !important;
    }

    /* Header search */
    .main-header .search-form input,
    .main-header .search-form .form-control,
    .header-main-container .search-form input,
    .header-main-container .search-form .form-control {
        background: rgba(0,0,0,0.15) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
        color: <?php echo $h_text_primary; ?> !important;
    }
    <?php if ($h_text === 'dark'): ?>
    .main-header .search-form input,
    .main-header .search-form .form-control,
    .header-main-container .search-form input,
    .header-main-container .search-form .form-control {
        background: rgba(255,255,255,0.8) !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
        color: #333 !important;
    }
    <?php endif; ?>
    .main-header .search-form input::placeholder,
    .main-header .search-form .form-control::placeholder {
        color: <?php echo $h_text_muted; ?> !important;
    }
    .main-header .search-form input:focus,
    .main-header .search-form .form-control:focus {
        background: rgba(0,0,0,0.2) !important;
        border-color: <?php echo $h_text === 'light' ? 'rgba(255,255,255,0.5)' : 'rgba(0,0,0,0.2)'; ?> !important;
        box-shadow: 0 0 5px rgba(<?php echo $h1_rgb; ?>, 0.3) !important;
    }

    /* Skin-specific header overrides */
    body.skin-blue .main-header > .logo,
    body.skin-blue .main-header > .logo:hover {
        background: <?php echo $header_darker; ?> !important;
    }
    body.skin-blue .main-header > .navbar {
        background-color: <?php echo $h1; ?> !important;
    }

    /* Accent UI elements use header colour */
    .btn-primary, .btn-primary:hover, .btn-primary:focus {
        background-color: <?php echo $h1; ?> !important;
        border-color: <?php echo $header_darker; ?> !important;
        color: <?php echo $h_text_primary; ?> !important;
    }
    .btn-primary.active, .btn-primary:active {
        background-color: <?php echo $header_darker; ?> !important;
    }
    .box-header.bg-aqua-gradient, .box-header.bg-light-blue-gradient {
        background: <?php echo $header_bg_css; ?> !important;
    }
    .box.box-primary { border-top-color: <?php echo $h1; ?> !important; }
    .box.box-solid.box-primary > .box-header {
        background: <?php echo $h1; ?> !important;
        color: <?php echo $h_text_primary; ?> !important;
    }
    .box.box-solid.box-primary { border: 1px solid <?php echo $h1; ?> !important; }
    .info-box-icon.bg-aqua, .info-box-icon.bg-light-blue {
        background-color: <?php echo $h1; ?> !important;
    }
    .pagination > .active > a, .pagination > .active > a:hover,
    .pagination > .active > a:focus, .pagination > .active > span,
    .pagination > .active > span:hover, .pagination > .active > span:focus {
        background-color: <?php echo $h1; ?> !important;
        border-color: <?php echo $header_darker; ?> !important;
    }
    .label-primary { background-color: <?php echo $h1; ?> !important; }
    .badge.bg-aqua, .badge.bg-light-blue { background-color: <?php echo $h1; ?> !important; }
    .main-header .navbar .nav > li > a > .label,
    .main-header .navbar .nav > li > a > .badge {
        background-color: #ff4757 !important;
    }
    .modal-header {
        background: <?php echo $h1; ?> !important;
        color: <?php echo $h_text_primary; ?> !important;
    }
    .modal-header .close { color: <?php echo $h_text_primary; ?> !important; opacity: 0.8; }
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover,
    .nav-tabs > li.active > a:focus {
        color: <?php echo $h1; ?> !important;
        border-top-color: <?php echo $h1; ?> !important;
    }
    .nav.nav-tabs-custom > li.active > a { border-bottom-color: <?php echo $h1; ?> !important; }
    .progress-bar.progress-bar-aqua, .progress-bar.progress-bar-light-blue {
        background-color: <?php echo $h1; ?> !important;
    }
    .sidebar-toggle { color: <?php echo $h_icon; ?> !important; }
    .sidebar-toggle:hover { color: <?php echo $h_icon_hover; ?> !important; }
    .header-main-container { background: <?php echo $header_bg_css; ?> !important; }
    <?php
    }

    // ===================== SIDEBAR SECTION =====================
    if ($has_sidebar) {
        $s1 = $tc_sidebar;
        $s2 = $tc_sidebar_g ? $tc_sidebar_g : $s1;
        $s1_rgb = _tc_rgb($s1);
        $s_text = _tc_text($s1);
        $s_text_main = ($s_text === 'light') ? 'rgba(255,255,255,0.75)' : 'rgba(0,0,0,0.7)';
        $s_text_active = ($s_text === 'light') ? '#ffffff' : '#000000';
        $s_text_sub = ($s_text === 'light') ? 'rgba(255,255,255,0.55)' : 'rgba(0,0,0,0.5)';
        $s_tree_line = ($s_text === 'light') ? 'rgba(255,255,255,0.15)' : 'rgba(0,0,0,0.1)';
        $s_tree_active = ($s_text === 'light') ? _tc_shift($s1, 60) : _tc_shift($s1, -30);
        $sidebar_bg_css = (strtolower($s1) === strtolower($s2))
            ? $s1
            : 'linear-gradient(180deg, ' . $s1 . ' 0%, ' . $s2 . ' 100%)';
        ?>
    /* ===== CUSTOM SIDEBAR: <?php echo $s1; ?> ===== */
    :root {
        --sidebar-bg: <?php echo $sidebar_bg_css; ?>;
        --sidebar-line-color: <?php echo $s_tree_line; ?>;
        --sidebar-line-active-color: <?php echo $s_tree_active; ?>;
        --sidebar-sub-hover-bg: rgba(<?php echo $s1_rgb; ?>, 0.2);
    }

    .main-sidebar, .main-sidebar .sidebar {
        background: <?php echo $sidebar_bg_css; ?> !important;
        background-image: none !important;
    }
    .left-side, .wrapper .left-side {
        background: <?php echo $sidebar_bg_css; ?> !important;
    }

    .sidebar-menu > li.header {
        color: <?php echo $s_text_sub; ?> !important;
        background: rgba(0,0,0,0.08) !important;
    }
    .sidebar-menu > li > a {
        color: <?php echo $s_text_main; ?> !important;
        border-left: 3px solid transparent !important;
    }
    .sidebar-menu > li:hover > a, .sidebar-menu > li.active > a {
        color: <?php echo $s_text_active; ?> !important;
        background: rgba(<?php echo $s1_rgb; ?>, 0.3) !important;
        border-left-color: <?php echo $s_tree_active; ?> !important;
    }

    .sidebar-menu > li > .treeview-menu {
        margin: 0 1px;
        background: rgba(0,0,0,0.08) !important;
    }
    .sidebar-menu > li > .treeview-menu > li > a {
        color: <?php echo $s_text_sub; ?> !important;
        padding-left: 35px !important;
    }
    .sidebar-menu > li > .treeview-menu > li > a:hover {
        color: <?php echo $s_text_active; ?> !important;
        background: rgba(<?php echo $s1_rgb; ?>, 0.25) !important;
    }
    .sidebar-menu > li > .treeview-menu > li.active > a {
        color: <?php echo $s_text_active; ?> !important;
        background: rgba(<?php echo $s1_rgb; ?>, 0.3) !important;
    }
    .sidebar-menu > li.treeview > a:hover,
    .sidebar-menu > li.treeview.active > a {
        color: <?php echo $s_text_active; ?> !important;
    }
    .sidebar-menu > li.treeview > a > .fa-angle-left {
        color: <?php echo $s_text_sub; ?> !important;
    }

    .sidebar-menu .treeview-menu::before { border-left-color: <?php echo $s_tree_line; ?> !important; }
    .sidebar-menu .treeview-menu > li::before { border-left-color: <?php echo $s_tree_line; ?> !important; }
    .sidebar-menu .treeview-menu > li.active::before { border-left-color: <?php echo $s_tree_active; ?> !important; }
    .sidebar-menu .treeview-menu > li.active::after { background: <?php echo $s_tree_active; ?> !important; }

    /* Sidebar search */
    .search-form2 input, .search-form2 .form-control {
        background: rgba(0,0,0,0.15) !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
        color: <?php echo $s_text === 'light' ? '#fff' : '#333'; ?> !important;
    }
    <?php if ($s_text === 'dark'): ?>
    .search-form2 input, .search-form2 .form-control {
        background: rgba(255,255,255,0.7) !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
        color: #333 !important;
    }
    <?php endif; ?>
    .search-form2 input::placeholder, .search-form2 .form-control::placeholder {
        color: <?php echo $s_text === 'light' ? 'rgba(255,255,255,0.4)' : 'rgba(0,0,0,0.3)'; ?> !important;
    }
    .search-form2 input:focus, .search-form2 .form-control:focus {
        background: rgba(0,0,0,0.2) !important;
        border-color: <?php echo $s_tree_active; ?> !important;
    }

    /* Skin-specific sidebar overrides */
    body.skin-blue .left-side, body.skin-blue .main-sidebar {
        background-color: <?php echo $s1; ?> !important;
    }

    .main-sidebar::-webkit-scrollbar-thumb {
        background: <?php echo $s_text === 'light' ? 'rgba(255,255,255,0.2)' : 'rgba(0,0,0,0.15)'; ?> !important;
    }
    .main-sidebar::-webkit-scrollbar-track {
        background: <?php echo $s_text === 'light' ? 'rgba(0,0,0,0.1)' : 'rgba(0,0,0,0.05)'; ?> !important;
    }
    <?php
    }

    // ===================== BODY / CONTENT SECTION =====================
    if ($has_body) {
        ?>
    /* ===== CUSTOM BODY BACKGROUND: <?php echo $tc_body_bg; ?> ===== */
    body { background-color: <?php echo $tc_body_bg; ?> !important; }
    .content-wrapper { background-color: <?php echo $tc_body_bg; ?> !important; }
    <?php
    }

    // ===================== ACCENT GRADIENT SECTION =====================
    if ($has_accent) {
        $ac_start = $tc_accent_start;
        $ac_end = $tc_accent_end ? $tc_accent_end : $ac_start;
        ?>
    /* ===== CUSTOM ACCENT GRADIENT: <?php echo $ac_start; ?> → <?php echo $ac_end; ?> ===== */
    :root {
        --accent-gradient-start: <?php echo $ac_start; ?>;
        --accent-gradient-end: <?php echo $ac_end; ?>;
    }
    .content-wrapper::before {
        background-image: linear-gradient(45deg, <?php echo $ac_start; ?>, <?php echo $ac_end; ?>) !important;
    }
    .modal-header {
        background: linear-gradient(135deg, <?php echo $ac_start; ?>, <?php echo $ac_end; ?>) !important;
    }
    <?php
    }
    ?>
    </style>
    <?php
}
