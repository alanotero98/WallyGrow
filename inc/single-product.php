<?php
/**
 * Single product (PDP) enhancements.
 *
 * Adds an optional WhatsApp advisory CTA under the add-to-cart area.
 * The button only renders when WhatsApp is configured.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Advisory deep-link for the current product, if any.
 *
 * @param WC_Product|null $product Product context.
 * @return string Empty when WhatsApp is not configured.
 */
function wally_grow_get_product_whatsapp_url($product = null) {
    if (!wally_grow_has_whatsapp()) {
        return '';
    }

    if (!$product instanceof WC_Product && function_exists('wc_get_product')) {
        $product = wc_get_product(get_the_ID());
    }

    $message = __('Hola Wally Grow, quiero asesoramiento sobre este producto', 'wally-grow-child');
    if ($product instanceof WC_Product) {
        $message .= ': ' . $product->get_name();
        $permalink = $product->get_permalink();
        if ($permalink) {
            $message .= ' — ' . $permalink;
        }
    }

    return wally_grow_get_whatsapp_url($message);
}

/**
 * Render WhatsApp CTA on the product summary.
 */
function wally_grow_render_product_whatsapp_cta() {
    if (!is_product()) {
        return;
    }

    $url = wally_grow_get_product_whatsapp_url();
    if ($url === '') {
        return;
    }
    ?>
    <p class="wg-product-whatsapp">
        <a
            class="button wg-product-whatsapp__button"
            href="<?php echo esc_url($url); ?>"
            target="_blank"
            rel="noopener noreferrer"
        >
            <?php esc_html_e('Consultar por WhatsApp', 'wally-grow-child'); ?>
        </a>
    </p>
    <?php
}
add_action('woocommerce_single_product_summary', 'wally_grow_render_product_whatsapp_cta', 35);
