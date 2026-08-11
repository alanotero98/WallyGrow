<?php
/**
 * Brand assets shared by the header, footer and browser icon.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Public URL for the bundled Wally Grow isotipo.
 */
function wally_grow_get_brand_icon_url() {
    return apply_filters(
        'wally_grow:branding:icon_url',
        get_stylesheet_directory_uri() . '/assets/images/branding/wally-isotipo.png'
    );
}

/**
 * Render the bundled isotipo as a decorative image next to the visible wordmark.
 */
function wally_grow_get_brand_icon_markup($class_name, $loading = 'lazy') {
    return sprintf(
        '<img class="%1$s" src="%2$s" alt="" width="512" height="512" loading="%3$s" decoding="async">',
        esc_attr($class_name),
        esc_url(wally_grow_get_brand_icon_url()),
        esc_attr($loading)
    );
}

/**
 * Use the bundled isotipo as favicon until a Site Icon is configured in WordPress.
 */
function wally_grow_render_fallback_site_icon() {
    if (has_site_icon()) {
        return;
    }

    $icon_url = wally_grow_get_brand_icon_url();
    ?>
    <link rel="icon" href="<?php echo esc_url($icon_url); ?>" sizes="32x32">
    <link rel="icon" href="<?php echo esc_url($icon_url); ?>" sizes="192x192">
    <link rel="apple-touch-icon" href="<?php echo esc_url($icon_url); ?>">
    <?php
}
add_action('wp_head', 'wally_grow_render_fallback_site_icon', 99);
