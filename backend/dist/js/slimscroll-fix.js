/**
 * SlimScroll Fix - Deep Fix
 * ==========================
 * Complete rewrite to properly handle SlimScroll initialization
 * The core issue: SlimScroll is destroyed and recreated but with incorrect height
 * Solution: Force proper re-calculation and use explicit mouse event handlers
 */

(function ($) {
    'use strict';

    var initialized = false;
    var lastHeight = 0;
    var resizeTimeout = null;

    /**
     * Main initialization function
     */
    function initSlimScrollFix() {

        // Check dependencies
        if (!$.AdminLTE || !$.AdminLTE.layout) {
            console.warn('⚠ AdminLTE not available yet, will retry...');
            setTimeout(initSlimScrollFix, 500);
            return;
        }

        if (typeof $.fn.slimScroll === 'undefined') {
            console.warn('⚠ SlimScroll plugin not found');
            return;
        }

        console.log('✓ All dependencies loaded');

        /**
         * Strategy: Override the fixSidebar function with our own better version
         * that ensures proper initialization with correct dimensions
         */
        if (!initialized) {
            initialized = true;
            overrideFixSidebar();
            setupEventListeners();
        }

        // Perform initial fix
        setTimeout(function () {
            fixSidebarProperly();
        }, 100);
    }

    /**
     * Override AdminLTE's fixSidebar with a better version
     */
    function overrideFixSidebar() {
        var original_fixSidebar = $.AdminLTE.layout.fixSidebar;

        $.AdminLTE.layout.fixSidebar = function () {
            // Call original first
            original_fixSidebar.call(this);

            // Then apply our enhancements
            fixSidebarProperly();
        };
    }

    /**
     * The core fix: Properly initialize SlimScroll with correct height
     * Now preserves scroll position across reinitializations
     */
    function fixSidebarProperly() {
        var $sidebar = $('.sidebar');
        var $body = $('body');
        var $header = $('.main-header');

        // Only proceed if body has fixed class
        if (!$body.hasClass('fixed')) {
            console.log('📝 Non-fixed layout detected, skipping SlimScroll');
            return;
        }

        if ($sidebar.length === 0) {
            console.warn('⚠ Sidebar not found');
            return;
        }

        if (typeof $.fn.slimScroll === 'undefined') {
            console.warn('⚠ SlimScroll not available');
            return;
        }

        try {
            // CRITICAL: Save current scroll position BEFORE destroying SlimScroll
            var currentScrollTop = $sidebar.scrollTop() || 0;
            var savedPosition = getSavedScrollPosition();
            var scrollToRestore = currentScrollTop > 0 ? currentScrollTop : savedPosition;

            console.log('📊 Saving scroll position before reinit:', scrollToRestore);

            // Calculate proper height - use innerHeight for more reliable detection
            // $(window).height() can return incorrect values in some cases
            var windowHeight = window.innerHeight || $(window).height();
            var headerHeight = $header.length > 0 ? $header.outerHeight() : 0;

            // Ensure we have reasonable values
            if (windowHeight > 2000 || windowHeight < 200) {
                console.warn('⚠ Unusual window height detected:', windowHeight, '- using fallback');
                windowHeight = document.documentElement.clientHeight || 800;
            }

            var calculatedHeight = windowHeight - headerHeight;

            console.log('📊 Dimensions - Window:', windowHeight, 'Header:', headerHeight, 'Calculated:', calculatedHeight);

            // CRITICAL: First, completely destroy any existing slimscroll
            $sidebar.slimScroll({ destroy: true });
            $sidebar.height('auto');

            // Clear inline height style that might be blocking
            $sidebar.css('height', '');

            // Remove any existing scrollbar elements
            $sidebar.parent().find('.slimScrollRail, .slimScrollBar').remove();

            // Small delay to ensure DOM update
            setTimeout(function () {
                try {
                    // Now initialize with proper settings
                    // Note: We don't set 'start' option here because we'll manually set scroll position
                    $sidebar.slimscroll({
                        height: calculatedHeight + 'px',
                        size: '6px',
                        color: 'rgba(0,0,0,0.3)',
                        position: 'right',
                        distance: '0px',
                        opacity: 0.6,
                        alwaysVisible: false,
                        disableFadeOut: false,
                        railVisible: false,
                        railColor: '#222',
                        railOpacity: 0.2,
                        wheelStep: 20,
                        touchScrollStep: 200,
                        borderRadius: '4px',
                        railBorderRadius: '4px'
                    });

                    // CRITICAL: Validate and restore scroll position
                    if (scrollToRestore > 0) {
                        // Get the actual scrollable area
                        var scrollHeight = $sidebar[0].scrollHeight;
                        var maxScroll = scrollHeight - calculatedHeight;

                        console.log('📊 Scroll bounds - Content height:', scrollHeight, 'Max scroll:', maxScroll);

                        // Ensure scroll position doesn't exceed max scrollable area
                        if (scrollToRestore > maxScroll && maxScroll > 0) {
                            console.log('⚠ Scroll position', scrollToRestore, 'exceeds max', maxScroll, '- adjusting');
                            scrollToRestore = maxScroll;
                        }

                        // Use multiple methods to ensure scroll position is set
                        $sidebar.scrollTop(scrollToRestore);

                        // Also set on the slimScrollDiv wrapper if it exists
                        var $wrapper = $sidebar.parent('.slimScrollDiv');
                        if ($wrapper.length) {
                            $wrapper.scrollTop(scrollToRestore);
                        }

                        // Update the scrollbar position to match
                        updateScrollBarPosition($sidebar, scrollToRestore, calculatedHeight);

                        console.log('✓ Scroll position restored to:', scrollToRestore);
                    }

                    // Force initialization by triggering mouse enter
                    $sidebar.trigger('mouseenter');

                    console.log('✓ SlimScroll properly initialized - Height: ' + calculatedHeight + 'px');

                } catch (e) {
                    console.error('✗ Error initializing SlimScroll:', e);
                }
            }, 10);

        } catch (e) {
            console.error('✗ Error in fixSidebarProperly:', e);
        }
    }

    /**
     * Update the SlimScroll scrollbar position to match the content scroll
     */
    function updateScrollBarPosition($sidebar, scrollTop, containerHeight) {
        var $bar = $sidebar.siblings('.slimScrollBar');
        if (!$bar.length) {
            $bar = $sidebar.parent().find('.slimScrollBar');
        }

        if ($bar.length) {
            var scrollHeight = $sidebar[0].scrollHeight;
            var barHeight = $bar.outerHeight();
            var maxScrollTop = scrollHeight - containerHeight;

            if (maxScrollTop > 0) {
                var barTop = (scrollTop / maxScrollTop) * (containerHeight - barHeight);
                $bar.css('top', barTop + 'px');
            }
        }
    }

    /**
     * Setup event listeners for re-initialization triggers
     */
    function setupEventListeners() {
        // Window load - critical initialization
        if (document.readyState === 'complete') {
            fixSidebarProperly();
        } else {
            $(window).on('load', function () {
                console.log('📝 Window load event fired');
                setTimeout(fixSidebarProperly, 150);
            });
        }

        // Tab visibility change
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                console.log('📝 Tab became visible, reinitializing...');
                setTimeout(fixSidebarProperly, 100);
            }
        }, false);

        // Window resize with smart detection
        $(window).on('resize', function () {
            if (resizeTimeout) {
                clearTimeout(resizeTimeout);
            }

            resizeTimeout = setTimeout(function () {
                var currentHeight = $(window).height();

                // Only if height changed significantly (> 30px)
                if (Math.abs(currentHeight - lastHeight) > 30) {
                    lastHeight = currentHeight;
                    console.log('📝 Window height changed, reinitializing...');
                    fixSidebarProperly();
                }
            }, 300);
        });

        // Force recalc on sidebar tree menu open/close
        $(document).on('click', '.sidebar .treeview > a', function () {
            setTimeout(function () {
                console.log('📝 Sidebar menu changed, recalculating...');

                // Get current slimscroll instance
                var $sidebar = $('.sidebar');
                if ($sidebar.length) {
                    // Trigger mouseenter to make scrollbar active
                    $sidebar.trigger('mouseenter');
                }
            }, 100);
        });

        // Ensure slimscroll stays active when sidebar is hovered
        $(document).on('mouseenter', '.sidebar', function () {
            var $sidebar = $('.sidebar');
            if ($sidebar.data('slimscroll')) {
                // Slimscroll is active, keep it visible
                $sidebar.find('.slimScrollBar').show();
            }
        });

        // Save sidebar scroll position when user scrolls - multiple event types for reliability
        $(document).on('scroll', '.sidebar, .slimScrollDiv', function () {
            saveSidebarScrollPosition();
        });

        // Capture wheel events directly on sidebar for better tracking
        $(document).on('wheel mousewheel DOMMouseScroll', '.sidebar, .slimScrollDiv', function () {
            // Debounce to avoid too many saves
            setTimeout(saveSidebarScrollPosition, 50);
        });

        // Capture touch events for mobile
        $(document).on('touchmove touchend', '.sidebar, .slimScrollDiv', function () {
            setTimeout(saveSidebarScrollPosition, 50);
        });

        // Also capture scroll via slimscroll events
        $('.sidebar').on('slimscrolling', function (e, pos) {
            saveSidebarScrollPosition();
        });

        // Periodic check to ensure scroll position is tracked (fallback)
        setInterval(function () {
            var $sidebar = $('.sidebar');
            if ($sidebar.length) {
                var currentScroll = $sidebar.scrollTop();
                var savedScroll = getSavedScrollPosition();
                // Only update if position changed significantly
                if (currentScroll > 0 && Math.abs(currentScroll - savedScroll) > 5) {
                    sessionStorage.setItem(SCROLL_STORAGE_KEY, currentScroll);
                }
            }
        }, 1000);

        console.log('✓ Event listeners installed');
    }

    /**
     * Storage key for sidebar scroll position
     */
    var SCROLL_STORAGE_KEY = 'sidebar_scroll_position';
    var SCROLL_VISITED_KEY = 'sidebar_initial_scroll_done';

    /**
     * Save current sidebar scroll position to sessionStorage
     */
    function saveSidebarScrollPosition() {
        try {
            var $sidebar = $('.sidebar');
            if ($sidebar.length) {
                var scrollTop = $sidebar.scrollTop();
                if (scrollTop > 0) {
                    sessionStorage.setItem(SCROLL_STORAGE_KEY, scrollTop);
                }
            }
        } catch (e) {
            // sessionStorage may not be available
        }
    }

    /**
     * Get saved scroll position from sessionStorage
     */
    function getSavedScrollPosition() {
        try {
            var saved = sessionStorage.getItem(SCROLL_STORAGE_KEY);
            return saved ? parseInt(saved, 10) : 0;
        } catch (e) {
            return 0;
        }
    }

    /**
     * Check if this is the initial visit to this page (not a tab switch back)
     */
    function isInitialPageLoad() {
        try {
            // Use performance.navigation or navigation timing
            if (window.performance && window.performance.navigation) {
                // TYPE_NAVIGATE = 0 (new page), TYPE_RELOAD = 1, TYPE_BACK_FORWARD = 2
                return window.performance.navigation.type === 0;
            }
            return true; // Assume initial load if we can't detect
        } catch (e) {
            return true;
        }
    }

    /**
     * Scroll sidebar to show the active menu item
     */
    function scrollToActiveMenuItem() {
        var $sidebar = $('.sidebar');
        if (!$sidebar.length) return;

        // Find active menu item (deepest active element)
        var $activeItem = $sidebar.find('li.active').last();

        if (!$activeItem.length) {
            // Try to find by menu-open class (expanded parent)
            $activeItem = $sidebar.find('.treeview.menu-open > a').parent();
        }

        if (!$activeItem.length) {
            console.log('📝 No active menu item found');
            return;
        }

        console.log('📝 Found active menu item:', $activeItem.find('> a').text().trim());

        // Calculate scroll position to bring active item into view
        var sidebarTop = $sidebar.offset().top;
        var sidebarHeight = $sidebar.height();
        var itemTop = $activeItem.offset().top;
        var itemHeight = $activeItem.outerHeight();

        // Calculate the relative position within sidebar
        var currentScroll = $sidebar.scrollTop();
        var itemRelativeTop = itemTop - sidebarTop + currentScroll;

        // Target position: center the item in the visible area, but not at the very top
        var targetScroll = itemRelativeTop - (sidebarHeight / 3);

        // Ensure we don't scroll negative
        targetScroll = Math.max(0, targetScroll);

        // Smooth scroll to target position
        setTimeout(function () {
            $sidebar.scrollTop(targetScroll);
            // Save this position
            saveSidebarScrollPosition();
            console.log('✓ Scrolled sidebar to active item, position:', targetScroll);
        }, 100);
    }

    /**
     * Restore sidebar scroll position from sessionStorage
     */
    function restoreSidebarScrollPosition() {
        var savedPosition = getSavedScrollPosition();
        if (savedPosition > 0) {
            var $sidebar = $('.sidebar');
            if ($sidebar.length) {
                setTimeout(function () {
                    $sidebar.scrollTop(savedPosition);
                    console.log('✓ Restored sidebar scroll position:', savedPosition);
                }, 100);
            }
        }
    }

    /**
     * Handle sidebar scroll on page load or tab visibility change
     */
    function handleSidebarScroll(isTabSwitch) {
        if (isTabSwitch) {
            // Tab switch: restore saved position
            restoreSidebarScrollPosition();
        } else {
            // Initial page load: scroll to active item
            var savedPosition = getSavedScrollPosition();

            // If there's a saved position from a recent navigation, use it for 1 second window
            // Otherwise scroll to active item
            if (savedPosition > 0 && sessionStorage.getItem(SCROLL_VISITED_KEY)) {
                // Coming back to the page, restore position
                restoreSidebarScrollPosition();
            } else {
                // Fresh load, scroll to active menu
                scrollToActiveMenuItem();
                sessionStorage.setItem(SCROLL_VISITED_KEY, 'true');
            }
        }
    }

    /**
     * Initialize sidebar scroll behavior
     */
    function initSidebarScrollBehavior() {
        // Wait for SlimScroll to be properly initialized
        setTimeout(function () {
            // Initial load - scroll to active item
            handleSidebarScroll(false);

            // Override visibility change handler to restore scroll position
            document.removeEventListener('visibilitychange', handleVisibilityForScroll);
            document.addEventListener('visibilitychange', handleVisibilityForScroll, false);
        }, 500);
    }

    /**
     * Handle visibility change for scroll position
     */
    function handleVisibilityForScroll() {
        if (!document.hidden) {
            console.log('📝 Tab became visible, restoring scroll position...');
            setTimeout(function () {
                handleSidebarScroll(true);
            }, 200);
        } else {
            // Tab is being hidden, save current position
            saveSidebarScrollPosition();
        }
    }

    // Initialize scroll behavior after slimscroll is ready
    $(window).on('load', function () {
        setTimeout(initSidebarScrollBehavior, 300);
    });

    // Also initialize on document ready as fallback
    $(document).ready(function () {
        setTimeout(initSidebarScrollBehavior, 800);
    });

    /**
     * Public API
     */
    window.SlimScrollFix = {
        reinit: function () {
            console.log('📝 Manual reinit requested');
            fixSidebarProperly();
        },

        destroy: function () {
            var $sidebar = $('.sidebar');
            if ($sidebar.length && typeof $.fn.slimScroll !== 'undefined') {
                $sidebar.slimScroll({ destroy: true }).height('auto');
                console.log('✓ SlimScroll destroyed');
            }
        }
    };

    // Start initialization when document is ready
    if (document.readyState === 'loading') {
        $(document).on('ready', initSlimScrollFix);
    } else {
        $(document).ready(initSlimScrollFix);
    }

    // Also try on window load just to be safe
    $(window).on('load', function () {
        if (!initialized) {
            console.log('📝 Initializing via window load event');
            initSlimScrollFix();
        }
    });

})(jQuery);
