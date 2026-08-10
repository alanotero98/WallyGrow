<?php
/**
 * General child theme setup hooks.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_stylesheet_directory() . '/inc/home-content.php';

if (!function_exists('wally_grow_child_register_block_patterns')) {
    function wally_grow_child_register_block_patterns() {
        register_block_pattern_category(
            'wally-grow',
            array('label' => __('Wally Grow', 'wally-grow-child'))
        );

        register_block_pattern(
            'wally-grow-child/home-completa',
            array(
                'title' => __('Home completa — Wally Grow', 'wally-grow-child'),
                'description' => _x('Home de Wally Grow basada en el diseño aprobado.', 'Block pattern description', 'wally-grow-child'),
                'content' => wally_grow_get_home_blocks(),
                'categories' => array('wally-grow', 'featured'),
                'viewportWidth' => 1440,
            )
        );
    }
}
add_action('init', 'wally_grow_child_register_block_patterns');

/**
 * Keep Blocksy's page title and content wrappers out of the bespoke front page.
 */
add_filter('blocksy:hero:enabled', function ($enabled) {
    return (is_front_page() || (function_exists('is_shop') && (is_shop() || is_product_taxonomy())))
        ? false
        : $enabled;
});

add_filter('blocksy:hero:custom-source', function ($source) {
    if (is_front_page() || (function_exists('is_shop') && (is_shop() || is_product_taxonomy()))) {
        return false;
    }

    return $source;
});
