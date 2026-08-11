<?php
/**
 * Ecommerce links injected into Blocksy navigation through WordPress hooks.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Navigation locations owned by the child theme and Blocksy builders.
 *
 * @return string[]
 */
function wally_grow_header_menu_locations() {
    return apply_filters(
        'wally_grow:header:menu_locations',
        array('menu_1', 'menu_mobile', 'primary')
    );
}

/**
 * Check whether a menu belongs to one of the public header locations.
 */
function wally_grow_is_header_menu($args) {
    $location = isset($args->theme_location) ? (string) $args->theme_location : '';
    return in_array($location, wally_grow_header_menu_locations(), true);
}

/**
 * Current cart count, without assuming WooCommerce initialized a cart.
 */
function wally_grow_get_cart_count() {
    if (!function_exists('WC') || !WC()->cart) {
        return 0;
    }

    return (int) WC()->cart->get_cart_contents_count();
}

/**
 * Cart quantity markup replaced by WooCommerce cart fragments.
 */
function wally_grow_get_cart_status_markup() {
    $count = wally_grow_get_cart_count();
    $count_class = $count === 0 ? ' wg-cart-count--empty' : '';
    $label = sprintf(
        _n('%d producto en el carrito', '%d productos en el carrito', $count, 'wally-grow-child'),
        $count
    );

    return sprintf(
        '<span class="wg-cart-status"><span class="wg-cart-count%1$s" aria-hidden="true">%2$d</span><span class="screen-reader-text">%3$s</span></span>',
        esc_attr($count_class),
        $count,
        esc_html($label)
    );
}

/**
 * Append stable ecommerce destinations without editing Blocksy templates.
 */
function wally_grow_append_header_ecommerce_links($items, $args) {
    if (
        is_admin()
        || !wally_grow_is_header_menu($args)
        || !function_exists('wc_get_cart_url')
        || strpos($items, 'wg-menu-item--cart') !== false
    ) {
        return $items;
    }

    $cart_url = wc_get_cart_url();
    $account_url = wc_get_page_permalink('myaccount');

    if ($cart_url) {
        $items .= sprintf(
            '<li class="menu-item wg-menu-item wg-menu-item--cart"><a class="ct-menu-link" href="%1$s"><span>%2$s</span>%3$s</a></li>',
            esc_url($cart_url),
            esc_html__('Carrito', 'wally-grow-child'),
            wally_grow_get_cart_status_markup()
        );
    }

    if ($account_url) {
        $items .= sprintf(
            '<li class="menu-item wg-menu-item wg-menu-item--account"><a class="ct-menu-link" href="%1$s">%2$s</a></li>',
            esc_url($account_url),
            esc_html__('Mi cuenta', 'wally-grow-child')
        );
    }

    return $items;
}
add_filter('wp_nav_menu_items', 'wally_grow_append_header_ecommerce_links', 20, 2);

/**
 * Keep the persisted "Categorías" item useful outside the Home page.
 */
function wally_grow_point_categories_menu_to_shop($items, $args) {
    if (!wally_grow_is_header_menu($args) || !function_exists('wc_get_page_permalink')) {
        return $items;
    }

    $shop_url = wc_get_page_permalink('shop');
    foreach ($items as $item) {
        if (isset($item->title) && sanitize_title($item->title) === 'categorias') {
            $item->url = $shop_url;
            $item->classes = array_values(array_diff(
                (array) $item->classes,
                array(
                    'current-menu-item',
                    'current_page_item',
                    'current-menu-ancestor',
                    'current_page_ancestor',
                )
            ));
        }
    }

    return $items;
}
add_filter('wp_nav_menu_objects', 'wally_grow_point_categories_menu_to_shop', 20, 2);

/**
 * Refresh every visible quantity badge after cart updates.
 */
function wally_grow_add_cart_count_fragment($fragments) {
    $fragments['.wg-cart-status'] = wally_grow_get_cart_status_markup();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'wally_grow_add_cart_count_fragment');

/**
 * Blocksy has no es_AR language pack; localize its public header controls.
 */
function wally_grow_translate_blocksy_header_controls($translated, $text) {
    $labels = array(
        'Search' => __('Buscar', 'wally-grow-child'),
        'Close search modal' => __('Cerrar búsqueda', 'wally-grow-child'),
        'Close drawer' => __('Cerrar menú', 'wally-grow-child'),
        'Menu' => __('Menú', 'wally-grow-child'),
    );

    return isset($labels[$text]) ? $labels[$text] : $translated;
}
add_filter('gettext_blocksy', 'wally_grow_translate_blocksy_header_controls', 10, 2);
