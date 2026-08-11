<?php
/**
 * Global Wally Grow footer.
 *
 * Blocksy keeps ownership of the semantic footer element and lifecycle. This
 * component replaces only the Footer Builder's inner output through its native
 * custom-output filter, keeping one source for every public template.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Link to a published page by path, or null when missing.
 *
 * @param string $path Page path without leading slash.
 * @param string $label Link label.
 * @return array{label:string,url:string}|null
 */
function wally_grow_footer_page_link($path, $label) {
    $page = get_page_by_path($path);
    if (!$page instanceof WP_Post || $page->post_status !== 'publish') {
        return null;
    }

    return array(
        'label' => $label,
        'url' => get_permalink($page),
    );
}

/**
 * Public names of WooCommerce gateways explicitly enabled by the store owner.
 *
 * @return string[]
 */
function wally_grow_get_enabled_payment_method_names() {
    if (!function_exists('WC') || !WC()->payment_gateways()) {
        return array();
    }

    $names = array();
    foreach (WC()->payment_gateways()->payment_gateways() as $gateway) {
        if (!isset($gateway->enabled) || $gateway->enabled !== 'yes') {
            continue;
        }

        $title = trim(wp_strip_all_tags((string) $gateway->get_title()));
        if ($title !== '') {
            $names[$title] = $title;
        }
    }

    return array_values($names);
}

/**
 * Central footer content.
 *
 * Developers can adjust all footer labels and links here, or filter the array
 * from another site-specific integration without duplicating markup.
 */
function wally_grow_get_footer_content() {
    $shop_url = function_exists('wc_get_page_permalink')
        ? wc_get_page_permalink('shop')
        : home_url('/');

    $category_links = array_values(array_filter(array(
        array(
            'label' => __('Iluminación LED', 'wally-grow-child'),
            'url' => wally_grow_product_cat_url(array('iluminacion', 'iluminacion-led', 'lighting'), $shop_url),
        ),
        array(
            'label' => __('Fertilizantes orgánicos', 'wally-grow-child'),
            'url' => wally_grow_product_cat_url(array('nutrientes', 'fertilizantes', 'fertilizantes-organicos'), $shop_url),
        ),
        array(
            'label' => __('Carpas y cultivo indoor', 'wally-grow-child'),
            'url' => wally_grow_product_cat_url(array('carpas', 'cultivo-indoor', 'indoor'), $shop_url),
        ),
        array(
            'label' => __('Accesorios de medición', 'wally-grow-child'),
            'url' => wally_grow_product_cat_url(array('accesorios', 'medicion', 'herramientas'), $shop_url),
        ),
    )));

    $help_links = array_values(array_filter(array(
        wally_grow_footer_page_link('preguntas-frecuentes', __('Preguntas frecuentes', 'wally-grow-child')),
        wally_grow_footer_page_link('politica-de-envios', __('Política de envíos', 'wally-grow-child')),
        wally_grow_footer_page_link('terminos-y-condiciones', __('Términos y condiciones', 'wally-grow-child')),
        wally_grow_footer_page_link('politica-de-privacidad', __('Política de privacidad', 'wally-grow-child')),
        wally_grow_footer_page_link('privacy-policy', __('Política de privacidad', 'wally-grow-child')),
        wally_grow_footer_page_link('soporte-tecnico', __('Soporte técnico', 'wally-grow-child')),
    )));

    // Deduplicate privacy if both slugs resolve to the same URL.
    $seen_urls = array();
    $help_links = array_values(array_filter($help_links, function ($link) use (&$seen_urls) {
        $url = isset($link['url']) ? (string) $link['url'] : '';
        if ($url === '' || isset($seen_urls[$url])) {
            return false;
        }
        $seen_urls[$url] = true;
        return true;
    }));

    $columns = array();
    if ($category_links) {
        $columns[] = array(
            'title' => __('Categorías', 'wally-grow-child'),
            'links' => $category_links,
        );
    }
    if ($help_links) {
        $columns[] = array(
            'title' => __('Ayuda', 'wally-grow-child'),
            'links' => $help_links,
        );
    }

    $details = array();
    $contact_items = array();
    $contact_email = wally_grow_get_contact_email();
    if ($contact_email !== '') {
        $contact_items[] = sprintf(
            '<a href="mailto:%1$s">%2$s</a>',
            esc_attr($contact_email),
            esc_html($contact_email)
        );
    }

    $whatsapp_url = wally_grow_get_whatsapp_url(
        __('Hola Wally Grow, tengo una consulta.', 'wally-grow-child')
    );
    if ($whatsapp_url !== '') {
        $contact_items[] = sprintf(
            '<a href="%1$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
            esc_url($whatsapp_url),
            esc_html__('WhatsApp', 'wally-grow-child')
        );
    }

    if ($contact_items) {
        $details[] = array(
            'title' => __('Contacto', 'wally-grow-child'),
            'items' => $contact_items,
        );
    }

    $payment_methods = wally_grow_get_enabled_payment_method_names();
    if ($payment_methods) {
        $details[] = array(
            'title' => __('Medios de pago', 'wally-grow-child'),
            'items' => array_map('esc_html', $payment_methods),
        );
    }

    $content = array(
        'description' => __('Excelencia botánica. Tu aliado para el cultivo indoor y exterior, con tecnología, asesoramiento y calidad.', 'wally-grow-child'),
        'columns' => $columns,
        'details' => $details,
    );

    return apply_filters('wally_grow:footer:content', $content);
}

/**
 * Render the single global footer body.
 */
function wally_grow_render_global_footer() {
    $content = wally_grow_get_footer_content();
    $site_name = get_bloginfo('name');
    $home_url = home_url('/');

    ob_start();
    ?>
    <div class="wg-global-footer">
        <div class="wg-global-footer__grid">
            <div class="wg-global-footer__brand">
                <a class="wg-global-footer__identity" href="<?php echo esc_url($home_url); ?>" rel="home">
                    <?php if (has_custom_logo()) : ?>
                        <?php
                        $logo_id = get_theme_mod('custom_logo');
                        echo wp_get_attachment_image(
                            $logo_id,
                            'full',
                            false,
                            array(
                                'class' => 'wg-global-footer__logo',
                                'alt' => '',
                                'loading' => 'lazy',
                            )
                        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    <?php else : ?>
                        <?php
                        echo wally_grow_get_brand_icon_markup(
                            'wg-global-footer__logo',
                            'lazy'
                        ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    <?php endif; ?>
                    <span><?php echo esc_html($site_name); ?></span>
                </a>
                <p><?php echo esc_html($content['description']); ?></p>
            </div>

            <?php foreach ($content['columns'] as $column) : ?>
                <nav class="wg-global-footer__column" aria-label="<?php echo esc_attr($column['title']); ?>">
                    <h2><?php echo esc_html($column['title']); ?></h2>
                    <ul>
                        <?php foreach ($column['links'] as $link) : ?>
                            <li><a href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            <?php endforeach; ?>

            <?php foreach ($content['details'] as $detail) : ?>
                <div class="wg-global-footer__column">
                    <h2><?php echo esc_html($detail['title']); ?></h2>
                    <ul>
                        <?php foreach ($detail['items'] as $item) : ?>
                            <li><?php echo wp_kses_post($item); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="wg-global-footer__bottom">
            <p>
                &copy; <?php echo esc_html(wp_date('Y')); ?>
                <?php echo esc_html($site_name); ?>.
                <?php esc_html_e('Excelencia botánica.', 'wally-grow-child'); ?>
            </p>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

/**
 * Force the same footer on every public Blocksy context and replace the
 * builder's row output using Blocksy's supported custom-output filter.
 */
add_filter('blocksy:builder:footer:enabled', '__return_true', PHP_INT_MAX);
add_filter('blocksy:builder:footer:custom-output', 'wally_grow_render_global_footer', PHP_INT_MAX);
