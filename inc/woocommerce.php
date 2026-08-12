<?php
/**
 * WooCommerce integration for Wally Grow.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_stylesheet_directory() . '/inc/product-search.php';

/**
 * WooCommerce theme supports live only here (not in functions.php) to avoid
 * duplicate owners of the same feature flags.
 */
function wally_grow_child_woocommerce_setup() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'wally_grow_child_woocommerce_setup');

function wally_grow_is_home_product_loop() {
    return is_front_page() && function_exists('wc_get_loop_prop');
}

/**
 * True for the public product-only search form supplied by this theme.
 */
function wally_grow_is_product_search_request() {
    $post_type = get_query_var('post_type');
    return is_search() && ($post_type === 'product' || $post_type === array('product'));
}

/**
 * Prefer a real brand attribute, then a useful product category.
 */
function wally_grow_get_product_commercial_label($product) {
    foreach (array('product_brand', 'pa_marca', 'pa_brand') as $taxonomy) {
        if (!taxonomy_exists($taxonomy)) {
            continue;
        }

        $brand = $product->get_attribute($taxonomy);
        if ($brand) {
            return wp_strip_all_tags($brand);
        }
    }

    $terms = get_the_terms($product->get_id(), 'product_cat');
    if (!$terms || is_wp_error($terms)) {
        return '';
    }

    $generic_terms = array('art-cultivo', 'sin-categoria', 'uncategorized');
    foreach ($terms as $term) {
        if (!in_array($term->slug, $generic_terms, true)) {
            return $term->name;
        }
    }

    return '';
}

function wally_grow_render_home_product_label() {
    global $product;

    if (!wally_grow_is_home_product_loop() || !($product instanceof WC_Product)) {
        return;
    }

    $label = wally_grow_get_product_commercial_label($product);
    if ($label) {
        echo '<p class="wg-product-card__category">' . esc_html($label) . '</p>';
    }
}
add_action('woocommerce_before_shop_loop_item_title', 'wally_grow_render_home_product_label', 25);

/**
 * Keep Home cards badge-free while preserving native prices and sale logic.
 */
add_filter('woocommerce_sale_flash', function ($html) {
    return wally_grow_is_home_product_loop() ? '' : $html;
});

/**
 * Keep /shop/ as the single canonical catalog URL. The old static /tienda/
 * page remains recoverable in WordPress but no longer competes with the shop.
 */
add_action('template_redirect', function () {
    if (!is_page('tienda') || !function_exists('wc_get_page_permalink')) {
        return;
    }

    wp_safe_redirect(wc_get_page_permalink('shop'), 301);
    exit;
});

/**
 * Keep the native WooCommerce loop and use three columns on desktop.
 */
add_filter('loop_shop_columns', function () {
    return 3;
});

/**
 * Render a catalog header inside Blocksy's WooCommerce content wrapper. Unlike
 * before_shop_loop, this hook also runs for a legitimate empty catalog.
 */
function wally_grow_render_shop_header() {
    if (!is_shop() && !is_product_taxonomy() && !wally_grow_is_product_search_request()) {
        return;
    }

    $description = '';
    $title = woocommerce_page_title(false);

    if (is_product_taxonomy()) {
        $description = term_description();
    } elseif (wally_grow_is_product_search_request()) {
        $title = sprintf(
            __('Resultados para “%s”', 'wally-grow-child'),
            get_search_query()
        );
    } elseif (is_shop()) {
        $title = __('Tienda', 'wally-grow-child');
        $shop_id = wc_get_page_id('shop');
        if ($shop_id > 0) {
            $description = get_post_field('post_excerpt', $shop_id);
            if (!$description) {
                $description = get_post_field('post_content', $shop_id);
            }
        }
    }
    ?>
    <header class="wg-shop-header">
        <div class="wg-shop-header__copy">
            <p class="wg-eyebrow"><?php esc_html_e('Catálogo', 'wally-grow-child'); ?></p>
            <h1><?php echo esc_html($title); ?></h1>
            <?php if ($description) : ?>
                <div class="wg-shop-header__description">
                    <?php echo wp_kses_post(wpautop(do_shortcode($description))); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php echo wally_grow_get_product_search_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </header>
    <?php
}
add_action('woocommerce_before_main_content', 'wally_grow_render_shop_header', 11);

/**
 * Replace WooCommerce's generic empty notice with useful recovery routes.
 */
function wally_grow_render_products_empty_state() {
    $shop_url = wc_get_page_permalink('shop');
    $home_url = home_url('/');
    $is_search = wally_grow_is_product_search_request();
    $terms = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'number' => 4,
        'parent' => 0,
    ));

    $title = $is_search
        ? sprintf(
            __('No encontramos resultados para “%s”', 'wally-grow-child'),
            get_search_query()
        )
        : __('Todavía no hay productos disponibles acá', 'wally-grow-child');

    $description = $is_search
        ? __('Probá con otro término o recorré las categorías disponibles.', 'wally-grow-child')
        : __('Podés volver a la tienda más adelante o seguir explorando el sitio.', 'wally-grow-child');
    ?>
    <section class="wg-empty-state" aria-labelledby="wg-products-empty-title">
        <p class="wg-eyebrow"><?php esc_html_e('Sin resultados', 'wally-grow-child'); ?></p>
        <h2 id="wg-products-empty-title"><?php echo esc_html($title); ?></h2>
        <p><?php echo esc_html($description); ?></p>

        <?php if (!is_wp_error($terms) && $terms) : ?>
            <nav class="wg-empty-state__categories" aria-label="<?php esc_attr_e('Categorías disponibles', 'wally-grow-child'); ?>">
                <?php foreach ($terms as $term) : ?>
                    <a href="<?php echo esc_url(get_term_link($term)); ?>">
                        <?php echo esc_html($term->name); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php endif; ?>

        <div class="wg-empty-state__actions">
            <?php if (!is_shop()) : ?>
                <a class="button" href="<?php echo esc_url($shop_url); ?>">
                    <?php esc_html_e('Ver toda la tienda', 'wally-grow-child'); ?>
                </a>
            <?php endif; ?>
            <a class="button wg-button--secondary" href="<?php echo esc_url($home_url); ?>">
                <?php esc_html_e('Volver al inicio', 'wally-grow-child'); ?>
            </a>
        </div>
    </section>
    <?php
}

function wally_grow_replace_products_empty_state() {
    remove_action('woocommerce_no_products_found', 'wc_no_products_found', 10);
    add_action('woocommerce_no_products_found', 'wally_grow_render_products_empty_state', 10);
}
add_action('wp', 'wally_grow_replace_products_empty_state');

/**
 * Show Blocksy's native WooCommerce sidebar only when it has widgets and the
 * catalog has useful category/attribute facets. Until then the loop is full-width.
 */
function wally_grow_shop_has_facets() {
    $has_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'number' => 1,
        'fields' => 'ids',
        'parent' => 0,
    ));

    if (!is_wp_error($has_categories) && !empty($has_categories)) {
        return true;
    }

    if (function_exists('wc_get_attribute_taxonomies')) {
        foreach (wc_get_attribute_taxonomies() as $attribute) {
            $taxonomy = wc_attribute_taxonomy_name($attribute->attribute_name);
            if (!taxonomy_exists($taxonomy)) {
                continue;
            }

            $term_count = wp_count_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => true,
            ));

            if (!is_wp_error($term_count) && $term_count > 0) {
                return true;
            }
        }
    }

    return false;
}

add_filter('blocksy:general:sidebar-position', function ($position) {
    if (!is_shop() && !is_product_taxonomy() && !wally_grow_is_product_search_request()) {
        return $position;
    }

    return is_active_sidebar('sidebar-woocommerce') && wally_grow_shop_has_facets()
        ? 'left'
        : 'none';
}, 100);

/**
 * Assets specific to product archives and the reusable search component.
 */
function wally_grow_enqueue_shop_assets() {
    if (!is_shop() && !is_product_taxonomy() && !wally_grow_is_product_search_request()) {
        return;
    }

    wp_enqueue_style(
        'wally-grow-shop',
        get_stylesheet_directory_uri() . '/assets/css/shop.css',
        array('wally-grow-child-components', 'ct-woocommerce-styles'),
        WALLY_GROW_CHILD_VERSION
    );

    wally_grow_enqueue_product_search_assets();
}
add_action('wp_enqueue_scripts', 'wally_grow_enqueue_shop_assets', 120);
