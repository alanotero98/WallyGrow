<?php
/**
 * Reusable, credential-free product autocomplete.
 */

if (!defined('ABSPATH')) {
    exit;
}

function wally_grow_enqueue_product_search_assets() {
    if (wp_script_is('wally-grow-product-search', 'enqueued')) {
        return;
    }

    if (!wp_style_is('wally-grow-shop', 'enqueued')) {
        wp_enqueue_style(
            'wally-grow-product-search',
            get_stylesheet_directory_uri() . '/assets/css/shop.css',
            array('wally-grow-child-components'),
            WALLY_GROW_CHILD_VERSION
        );
    }

    wp_enqueue_script(
        'wally-grow-product-search',
        get_stylesheet_directory_uri() . '/assets/js/product-search.js',
        array(),
        WALLY_GROW_CHILD_VERSION,
        true
    );

    wp_localize_script(
        'wally-grow-product-search',
        'wallyProductSearch',
        array(
            'endpoint' => esc_url_raw(rest_url('wally-grow/v1/product-search')),
            'minChars' => 2,
            'messages' => array(
                'loading' => __('Buscando productos…', 'wally-grow-child'),
                'empty' => __('No encontramos productos.', 'wally-grow-child'),
                'error' => __('No pudimos completar la búsqueda.', 'wally-grow-child'),
            ),
        )
    );
}

/**
 * Future header integrations can return true from this filter to preload the
 * component assets in wp_head before rendering its markup.
 */
add_action('wp_enqueue_scripts', function () {
    if (apply_filters('wally_grow:product-search:enqueue', false)) {
        wally_grow_enqueue_product_search_assets();
    }
}, 125);

function wally_grow_get_product_search_markup() {
    $search_id = wp_unique_id('wg-product-search-');
    $results_id = $search_id . '-results';

    ob_start();
    ?>
    <form class="wg-product-search" role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get" data-wg-product-search>
        <label class="screen-reader-text" for="<?php echo esc_attr($search_id); ?>">
            <?php esc_html_e('Buscar productos', 'wally-grow-child'); ?>
        </label>
        <div class="wg-product-search__field">
            <svg aria-hidden="true" viewBox="0 0 24 24" width="20" height="20">
                <path d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            <input
                id="<?php echo esc_attr($search_id); ?>"
                class="wg-product-search__input"
                type="search"
                name="s"
                placeholder="<?php esc_attr_e('Buscar un producto…', 'wally-grow-child'); ?>"
                autocomplete="off"
                minlength="2"
                aria-autocomplete="list"
                aria-controls="<?php echo esc_attr($results_id); ?>"
                aria-expanded="false"
                aria-haspopup="listbox"
                role="combobox"
            >
            <span class="wg-product-search__spinner" aria-hidden="true"></span>
        </div>
        <input type="hidden" name="post_type" value="product">
        <div
            id="<?php echo esc_attr($results_id); ?>"
            class="wg-product-search__dropdown"
            role="listbox"
            aria-label="<?php esc_attr_e('Sugerencias de productos', 'wally-grow-child'); ?>"
            hidden
        ></div>
        <p class="screen-reader-text wg-product-search__live" aria-live="polite" aria-atomic="true"></p>
    </form>
    <?php
    return ob_get_clean();
}

function wally_grow_product_search_shortcode() {
    wally_grow_enqueue_product_search_assets();
    return wally_grow_get_product_search_markup();
}
add_shortcode('wally_product_search', 'wally_grow_product_search_shortcode');

function wally_grow_rest_product_search(WP_REST_Request $request) {
    $query = sanitize_text_field((string) $request->get_param('q'));

    if (mb_strlen($query) < 2) {
        return rest_ensure_response(array());
    }

    $products_query = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        's' => $query,
        'posts_per_page' => 8,
        'no_found_rows' => true,
        'ignore_sticky_posts' => true,
        'orderby' => 'relevance',
        'order' => 'DESC',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => array('exclude-from-search'),
                'operator' => 'NOT IN',
            ),
        ),
    ));

    $results = array();

    foreach ($products_query->posts as $post) {
        $product = wc_get_product($post->ID);
        if (!$product) {
            continue;
        }

        $category_names = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'names'));
        $stock = '';

        if (!$product->is_in_stock()) {
            $stock = __('Sin stock', 'wally-grow-child');
        } elseif ($product->managing_stock() && null !== $product->get_stock_quantity()) {
            $stock = sprintf(
                _n('%d disponible', '%d disponibles', $product->get_stock_quantity(), 'wally-grow-child'),
                $product->get_stock_quantity()
            );
        } else {
            $stock = __('En stock', 'wally-grow-child');
        }

        $image_id = $product->get_image_id();
        $results[] = array(
            'id' => $product->get_id(),
            'name' => wp_strip_all_tags($product->get_name()),
            'url' => esc_url_raw($product->get_permalink()),
            'image' => $image_id
                ? esc_url_raw(wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail'))
                : esc_url_raw(wc_placeholder_img_src('woocommerce_thumbnail')),
            'price' => html_entity_decode(
                wp_strip_all_tags($product->get_price_html()),
                ENT_QUOTES,
                get_bloginfo('charset')
            ),
            'category' => !is_wp_error($category_names) ? implode(', ', array_slice($category_names, 0, 2)) : '',
            'stock' => $stock,
            'inStock' => $product->is_in_stock(),
        );
    }

    wp_reset_postdata();

    return rest_ensure_response($results);
}

add_action('rest_api_init', function () {
    register_rest_route(
        'wally-grow/v1',
        '/product-search',
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'wally_grow_rest_product_search',
            'permission_callback' => '__return_true',
            'args' => array(
                'q' => array(
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function ($value) {
                        return is_string($value) && mb_strlen(trim($value)) >= 2;
                    },
                ),
            ),
        )
    );
});
