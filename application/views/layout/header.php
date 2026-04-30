<!DOCTYPE html>
<html <?php echo $this->customlib->getRTL(); ?>>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $this->customlib->getAppName(); ?></title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta name="theme-color" content="#424242" />
    <link href="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo(); ?>" rel="shortcut icon" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/jquery.mCustomScrollbar.min.css">
    <?php $this->load->view('layout/theme'); ?>

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ss-print.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/iCheck/flat/blue.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/morris/morris.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/datepicker/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/themes/<?php echo $this->customlib->getCurrentTheme(); ?>/datepicker/bootstrap-datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/custom_style.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/quick-links-modal.css">

    <!--file dropify-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/dropify.min.css">
    <!--file nprogress-->
    <link href="<?php echo base_url(); ?>backend/dist/css/nprogress.css" rel="stylesheet">

    <!--print table-->
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <!--print table mobile support-->
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>backend/dist/datatables/css/rowReorder.dataTables.min.css" rel="stylesheet">
    <!--language css-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>backend/dist/css/bootstrap-select.min.css">
    <!--SumoSelect css-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets1/vendor/bootstrap-multiselect/sumoselect.css">
    <script src="<?php echo base_url(); ?>backend/custom/jquery.min.js"></script>
    <script language="javascript" src="<?php echo base_url(); ?>backend/custom/jquery-2.2.4.js"></script>
    <script src="<?php echo base_url(); ?>backend/dist/js/moment.min.js"></script>

    <script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
    <script src="<?php echo base_url(); ?>backend/plugins/colorpicker/bootstrap-colorpicker.js"></script>

    <script src="<?php echo base_url(); ?>backend/dist/js/jquery-ui.min.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/school-custom.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/school-admin-custom.js"></script>
    <script src="<?php echo base_url(); ?>backend/js/sstoast.js"></script>
    <!--SumoSelect js-->
    <script src="<?php echo base_url(); ?>assets1/vendor/bootstrap-multiselect/jquery.sumoselect.js"></script>

    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>backend/fullcalendar/dist/fullcalendar.print.min.css" media="print">
    <script type="text/javascript">
        var baseurl = "<?php echo base_url(); ?>";
        var start_week = <?php echo $this->customlib->getStartWeek(); ?>;
        var chk_validate = "<?php echo $this->config->item('SSLK') ?>";
    </script>
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
    <?php
    if ($this->config->item('SSLK') == "") {
    ?>
        <div class="topaleart">
            <div class="slidealert">
                <div class="alert alert-dismissible topaleart-inside">
                    <!--<p class="palert"><strong>Alert!</strong> You are using unregistered version of Smart School. Please <a  href="#" class="purchasemodal">click here</a> to register your purchase code for Smart School.</p> -->
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <script>
        function updateSidebarToggleIcon() {
            var body = document.getElementsByTagName('body')[0];
            var icon = document.querySelector('.sidebar-toggle-icon');
            if (!icon) {
                return;
            }

            var isMobile = window.innerWidth < 768;
            var isCollapsedOrHidden;

            if (isMobile) {
                // On small screens AdminLTE uses 'sidebar-open' when the menu is visible
                isCollapsedOrHidden = !body.classList.contains('sidebar-open');
            } else {
                // On desktop AdminLTE uses 'sidebar-collapse' when the menu is collapsed
                isCollapsedOrHidden = body.classList.contains('sidebar-collapse');
            }

            if (isCollapsedOrHidden) {
                // Sidebar is collapsed/hidden – show the "unfold" icon
                icon.classList.remove('ri-sidebar-fold-line');
                icon.classList.add('ri-sidebar-unfold-line');
            } else {
                // Sidebar is visible – show the "fold" icon
                icon.classList.remove('ri-sidebar-unfold-line');
                icon.classList.add('ri-sidebar-fold-line');
            }
        }

        function collapseSidebar() {
            // Wait for AdminLTE to update the class (approx 50-100ms)
            setTimeout(function() {
                var body = document.getElementsByTagName('body')[0];
                var isMobile = window.innerWidth < 768;
                var isCollapsed = false;

                if (isMobile) {
                    // Mobile: 'sidebar-open' means OPEN. Absence means COLLAPSED.
                    if (!body.classList.contains('sidebar-open')) {
                        isCollapsed = true;
                    }
                } else {
                    // Desktop: 'sidebar-collapse' means COLLAPSED. Absence means OPEN.
                    if (body.classList.contains('sidebar-collapse')) {
                        isCollapsed = true;
                    }
                }

                if (isCollapsed) {
                    sessionStorage.setItem('sidebar-toggle-collapsed', '1');
                } else {
                    sessionStorage.setItem('sidebar-toggle-collapsed', '');
                }

                updateSidebarToggleIcon();
            }, 100);
        }

        function checksidebar() {
            // Restore state on load, primarily for desktop
            if (window.innerWidth >= 768) {
                if (Boolean(sessionStorage.getItem('sidebar-toggle-collapsed'))) {
                    var body = document.getElementsByTagName('body')[0];
                    if (!body.classList.contains('sidebar-collapse')) {
                        body.classList.add('sidebar-collapse');
                    }
                }
            }
            updateSidebarToggleIcon();
        }

        checksidebar();

        // Keep icon correct when resizing
        window.addEventListener('resize', function() {
            updateSidebarToggleIcon();
        });
    </script>

    <div class="wrapper">
        <header class="main-header" id="alert">
            <a href="<?php echo base_url(); ?>admin/admin/dashboard" class="logo">
                <span class="logo-mini"><img src="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_small_logo/<?php echo $this->setting_model->getAdminsmalllogo() . img_time(); ?>" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
                <span class="logo-lg"><img src="<?php echo $this->customlib->getBaseUrl(); ?>uploads/school_content/admin_logo/<?php echo $this->setting_model->getAdminlogo() . img_time(); ?>" alt="<?php echo $this->customlib->getAppName() ?>" /></span>
            </a>
            <nav class="navbar navbar-static-top" role="navigation">
                <a onclick="collapseSidebar()" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only"><?php echo $this->lang->line('toggle_navigation'); ?></span>
                    <i class="ri-sidebar-fold-line sidebar-toggle-icon"></i>
                </a>
                <div class="col-lg-5 col-md-3 col-sm-7 col-xs-7">
                    <span href="#" class="sidebar-session playfair-display-title">
                        <?php echo $this->setting_model->getCurrentSchoolName(); ?>
                    </span>
                </div>
                <div class="col-lg-7 col-md-9 col-sm-5 col-xs-5">
                    <!--<div>-->
                    <div class="pull-right">
                        <div class="navbar-custom-menu">
                            <ul class="nav navbar-nav headertopmenu">
                                <?php if ($this->rbac->hasPrivilege('student', 'can_view')) { ?>
                                    <li class="header-search-li">
                                        <form id="header_search_form" class="navbar-form search-form" role="search" action="<?php echo site_url('admin/admin/searchhole'); ?>" method="POST">
                                            <?php echo $this->customlib->getCSRF(); ?>
                                            <div class="input-group header-search-wrapper" data-header-search>
                                                <input type="text" value="<?php echo set_value('search_text2'); ?>" name="search_text2" id="search_text2" class="form-control search-form search-form3" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>" autocomplete="off">
                                                <span class="input-group-btn">
                                                    <button type="submit" name="search" id="search-btn" style="" class="btn btn-flat topsidesearchbtn header-search-icon"><i class="fa fa-search"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </li>
                                <?php } ?>

                                <!-- <?php $userdata = $this->customlib->getUserData();
                                        if ($userdata["role_id"] == 7) {
                                            if (($this->module_lib->hasModule('multi_branch') && $this->module_lib->hasActive('multi_branch')) || $this->db->multi_branch) { ?>
                                
                                        <li class="cal15" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('switch_branch'); ?>"><a href="#" data-toggle="modal" data-target="#multiBranchSwitchModal"><i class="fa fa-exchange" aria-hidden="true"></i></a></li>

                                <?php }
                                        } ?> -->

                                <?php
                                if ($this->module_lib->hasActive('calendar_to_do_list')) {
                                    if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
                                ?>
                                        <li class="cal15 d-sm-none"><a data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('calendar') ?>" href="<?php echo base_url() ?>admin/calendar/events"><i class="ri-calendar-schedule-fill"></i></a> </li>
                                <?php
                                    }
                                }
                                ?>
                                <?php
                                if ($this->module_lib->hasActive('calendar_to_do_list')) {
                                    if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
                                ?>
                                        <li class="dropdown" data-placement="bottom" data-toggle="tooltip" title="<?php echo $this->lang->line('task') ?>">
                                            <a href="#" class="dropdown-toggle todoicon" data-toggle="dropdown">
                                                <i class="ri-todo-fill"></i>
                                                <?php
                                                $userdata = $this->customlib->getUserData();
                                                $count    = $this->customlib->countincompleteTask($userdata["id"], $userdata["role_id"]);
                                                if ($count > 0) {
                                                ?>
                                                    <span class="todo-indicator"><?php echo $count ?></span>
                                                <?php } ?>
                                            </a>
                                            <ul class="dropdown-menu menuboxshadow">

                                                <li class="todoview plr10 ssnoti"><?php echo $this->lang->line('today_you_have'); ?> <?php echo $count; ?> <?php echo $this->lang->line('pending_task'); ?><a href="<?php echo base_url() ?>admin/calendar/events" class="pull-right pt0"><?php echo $this->lang->line('view_all'); ?></a></li>
                                                <li>
                                                    <ul class="todolist">
                                                        <?php
                                                        $tasklist = $this->customlib->getincompleteTask($userdata["id"], $userdata["role_id"]);
                                                        foreach ($tasklist as $key => $value) {
                                                        ?>
                                                            <li>
                                                                <div class="checkbox">
                                                                    <label><input type="checkbox" id="newcheck<?php echo $value["id"] ?>" onclick="markc('<?php echo $value["id"] ?>')" name="eventcheck" value="<?php echo $value["id"]; ?>"><?php echo $value["event_title"] ?></label>
                                                                </div>
                                                            </li>
                                                        <?php } ?>

                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>

                                        <li class="dropdown d-lg-none d-sm-block ellipsis-px-3">
                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <ul class="dropdown-menu min-w-full sm-drop-down">
                                                <?php
                                                // Calendar
                                                $userdata = $this->customlib->getUserData();
                                                if ($this->module_lib->hasActive('calendar_to_do_list')) {
                                                    if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) { ?>
                                                        <li><a href="<?php echo base_url() ?>admin/calendar/events"><i class="fa fa-calendar"></i> <?php echo $this->lang->line('calendar'); ?></a></li>
                                                    <?php }
                                                }

                                                // Tasks
                                                if ($this->module_lib->hasActive('calendar_to_do_list')) {
                                                    if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
                                                        $count = $this->customlib->countincompleteTask($userdata["id"], $userdata["role_id"]); ?>
                                                        <li><a href="<?php echo base_url() ?>admin/calendar/events"><i class="fa fa-check-square-o"></i> <?php echo $this->lang->line('task'); ?> <?php if ($count > 0) { ?><span class="badge badge-danger"><?php echo $count; ?></span><?php } ?></a></li>
                                                    <?php }
                                                }

                                                // WhatsApp/Chat
                                                if ($this->module_lib->hasActive('chat')) {
                                                    if ($this->rbac->hasPrivilege('chat', 'can_view')) { ?>
                                                        <li><a href="<?php echo base_url() ?>admin/chat"><i class="fa fa-whatsapp"></i> <?php echo $this->lang->line('chat'); ?></a></li>
                                                <?php }
                                                }
                                                ?>
                                            </ul>
                                        </li>
                                <?php
                                    }
                                }
                                ?>
                                <?php
                                if ($this->module_lib->hasActive('chat')) {
                                    if ($this->rbac->hasPrivilege('chat', 'can_view')) {
                                ?>
                                        <li class="cal15 d-sm-none"><a data-placement="bottom" data-toggle="tooltip" title="" href="<?php echo base_url() ?>admin/chat" data-original-title="<?php echo $this->lang->line('chat') ?>" class="todoicon"><i class="ri-message-ai-3-fill"></i></a></li>
                                    <?php } ?>
                                <?php }
                                $file   = "";
                                $result = $this->customlib->getLoggedInUserData();
                                $role = $this->customlib->getStaffRole();


                                $image = $result["image"];
                                $role  = json_decode($role)->name;
                                $id    = $result["id"];
                                if (!empty($image)) {

                                    $file = "uploads/staff_images/" . $image . img_time();
                                } else {
                                    if ($result['gender'] == 'Female') {
                                        $file = "uploads/staff_images/default_female.jpg" . img_time();
                                    } else {
                                        $file = "uploads/staff_images/default_male.jpg" . img_time();
                                    }
                                }
                                ?>

                                <li class="dropdown user-menu">
                                    <a class="dropdown-toggle" style="padding: 15px 12px;" data-toggle="dropdown" href="#" aria-expanded="false">
                                        <img src="<?php echo base_url($file); ?>" class="topuser-image" alt="User Image">
                                    </a>
                                    <ul class="dropdown-menu dropdown-user menuboxshadow">
                                        <li>
                                            <div class="sstopuser">
                                                <div class="ssuserleft">
                                                    <a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>"><img src="<?php echo base_url($file); ?>" alt="User Image"></a>
                                                </div>
                                                <div class="sstopuser-test">
                                                    <h4 class="text-capitalize"><?php echo $this->customlib->getAdminSessionUserName(); ?></h4>
                                                    <h5><?php echo $role; ?></h5>
                                                </div>
                                                <div class="divider"></div>
                                                <div class="sspass">
                                                    <a href="<?php echo base_url() . "admin/staff/profile/" . $id ?>"><i class="fa fa-user"></i><?php echo $this->lang->line('profile'); ?> </a>
                                                    <a class="pl25" href="<?php echo base_url(); ?>admin/admin/changepass"><i class="fa fa-key"></i><?php echo $this->lang->line('password'); ?></a> <a class="pull-right" href="<?php echo base_url(); ?>site/logout"><i class="fa fa-sign-out fa-fw"></i><?php echo $this->lang->line('logout'); ?></a>
                                                </div>
                                            </div><!--./sstopuser-->
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
    </div>
    </nav>
    </header>

    <?php $this->load->view('layout/sidebar'); ?>
    <script>
        function set_languages(lang_id) {
            $.ajax({
                type: "POST",
                url: base_url + "admin/language/user_language/" + lang_id,
                data: {},
                success: function(data) {
                    successMsg("<?php echo $this->lang->line('status_change_successfully'); ?>");
                    window.location.reload('true');
                }
            });
        }

        function setCurrency(currency_id) {
            $.ajax({
                type: "POST",
                url: base_url + "admin/currency/change_currency",
                data: {
                    currency_id: currency_id
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        successMsg(data.message);
                        window.location.reload('true');
                    }
                }
            });
        }

        // Remove tooltips from bootstrap-select dropdowns
        $(document).ready(function() {
            // Remove data-original-title from language and currency selectors
            $('.langdiv .bootstrap-select .btn, .currency-icon-list .bootstrap-select .btn').each(function() {
                $(this).removeAttr('data-original-title');
                $(this).removeAttr('title');
                $(this).tooltip('destroy').off('mouseenter mouseleave');
            });

            // Prevent tooltip from being added again
            $('.languageselectpicker').on('loaded.bs.select rendered.bs.select', function() {
                $(this).parent().find('.btn').removeAttr('data-original-title').removeAttr('title');
            });

            // Handle dropdown submenu on click for mobile and tablet
            $('.dropdown-submenu > a').on('click', function(e) {
                var submenu = $(this).next('.dropdown-menu');
                if (submenu.length) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Close other submenus
                    $('.dropdown-submenu .dropdown-menu').removeClass('show');

                    // Toggle this submenu
                    submenu.toggleClass('show');
                }
            });

            // Close submenus when main dropdown closes
            $('.dropdown').on('hidden.bs.dropdown', function() {
                $(this).find('.dropdown-submenu .dropdown-menu').removeClass('show');
            });

            // Prevent dropdown from closing when clicking inside submenu
            $('.dropdown-submenu .dropdown-menu').on('click', function(e) {
                e.stopPropagation();
            });

            (function initAdminPageContextHeader() {
                var $content = $('.content-wrapper > .content').first();
                if (!$content.length) {
                    return;
                }

                if ($content.children('[data-admin-page-context]').length) {
                    return;
                }

                var $sidebar = $('.main-sidebar .sidebar-menu');
                if (!$sidebar.length) {
                    return;
                }

                // Find the correct active menu context
                // Priority: Find a treeview where BOTH parent AND a submenu item have 'active' class
                var menuText = '';
                var submenuText = '';
                
                var $treeviews = $sidebar.find('li.treeview.active');
                
                $treeviews.each(function() {
                    var $treeview = $(this);
                    var $activeSubmenu = $treeview.find('ul.treeview-menu > li.active > a').first();
                    
                    if ($activeSubmenu.length) {
                        // Found a treeview with an active parent AND an active submenu item
                        // This is the correct menu context
                        var $parentLink = $treeview.children('a').first();
                        var $menuSpan = $parentLink.find('span').first();
                        menuText = $.trim($menuSpan.text() || $parentLink.text());
                        
                        var $submenuSpan = $activeSubmenu.find('span').first();
                        submenuText = $.trim($submenuSpan.text() || $activeSubmenu.text());
                        
                        return false; // Break out of .each() loop
                    }
                });
                
                // Fallback: If no matching treeview found, use the old approach
                if (!menuText) {
                    var $activeLink = $sidebar.find('li.active > a').last();
                    if (!$activeLink.length) {
                        return;
                    }

                    var $submenuSpan = $activeLink.find('span').first();
                    submenuText = $.trim($submenuSpan.text() || $activeLink.text());

                    var $treeviewMenu = $activeLink.closest('ul.treeview-menu');

                    if ($treeviewMenu.length) {
                        var $parentLink = $treeviewMenu.closest('li.treeview').children('a').first();
                        var $menuSpan = $parentLink.find('span').first();
                        menuText = $.trim($menuSpan.text() || $parentLink.text());
                    } else {
                        menuText = submenuText;
                        submenuText = '';
                    }
                }

                if (!menuText) {
                    return;
                }

                var html = '<div class="admin-page-context" data-admin-page-context>' +
                    '<div class="admin-page-context__title">' +
                    '<span class="admin-page-context__menu"></span>' +
                    (submenuText ? '<span class="admin-page-context__sep"><i class="ri-arrow-right-double-fill"></i></span><span class="admin-page-context__submenu"></span>' : '') +
                    '</div>' +
                    '</div>';

                var $el = $(html);
                $el.find('.admin-page-context__menu').text(menuText);
                if (submenuText) {
                    $el.find('.admin-page-context__submenu').text(submenuText);
                }

                $content.prepend($el);
                $('.content-wrapper > .content-header').hide();
            })();

            (function initHeaderSearchToggle() {
                var $form = $('#header_search_form');
                if (!$form.length) {
                    return;
                }

                var $wrapper = $form.find('[data-header-search]');
                var $input = $form.find('#search_text2');
                var $iconBtn = $form.find('.header-search-icon');

                if (!$wrapper.length || !$input.length || !$iconBtn.length) {
                    return;
                }

                var expandedClass = 'header-search--expanded';

                function isDesktopHeaderMode() {
                    return window.innerWidth >= 992;
                }

                function expand() {
                    $wrapper.addClass(expandedClass);
                    setTimeout(function() {
                        $input.trigger('focus');
                    }, 10);
                }

                function collapse() {
                    $wrapper.removeClass(expandedClass);
                }

                $iconBtn.on('click', function(e) {
                    if (!isDesktopHeaderMode()) {
                        return;
                    }

                    if (!$wrapper.hasClass(expandedClass)) {
                        e.preventDefault();
                        expand();
                    }
                });

                $form.on('submit', function() {
                    if (isDesktopHeaderMode()) {
                        collapse();
                    }
                    if (typeof window.getstudentlist === 'function') {
                        window.getstudentlist();
                    }
                });

                $(document).on('click', function(e) {
                    if (!isDesktopHeaderMode()) {
                        return;
                    }
                    if ($wrapper.hasClass(expandedClass) && !$(e.target).closest('#header_search_form').length) {
                        collapse();
                    }
                });

                window.addEventListener('resize', function() {
                    if (!isDesktopHeaderMode()) {
                        collapse();
                    }
                });
            })();
        });
    </script>