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
 * Central footer content.
 *
 * Developers can adjust all footer labels and links here, or filter the array
 * from another site-specific integration without duplicating markup.
 */
function wally_grow_get_footer_content() {
    $shop_url = function_exists('wc_get_page_permalink')
        ? wc_get_page_permalink('shop')
        : home_url('/tienda/');

    $content = array(
        'description' => __('Botanical Excellence. Tu socio profesional en el cultivo indoor y exterior. Tecnología, asesoramiento y calidad.', 'wally-grow-child'),
        'columns' => array(
            array(
                'title' => __('Categorías', 'wally-grow-child'),
                'links' => array(
                    array('label' => __('Iluminación LED', 'wally-grow-child'), 'url' => $shop_url),
                    array('label' => __('Fertilizantes orgánicos', 'wally-grow-child'), 'url' => $shop_url),
                    array('label' => __('Carpas y cultivo indoor', 'wally-grow-child'), 'url' => $shop_url),
                    array('label' => __('Accesorios de medición', 'wally-grow-child'), 'url' => $shop_url),
                ),
            ),
            array(
                'title' => __('Ayuda', 'wally-grow-child'),
                'links' => array(
                    array('label' => __('Preguntas frecuentes', 'wally-grow-child'), 'url' => home_url('/preguntas-frecuentes/')),
                    array('label' => __('Política de envíos', 'wally-grow-child'), 'url' => home_url('/politica-de-envios/')),
                    array('label' => __('Términos y condiciones', 'wally-grow-child'), 'url' => home_url('/terminos-y-condiciones/')),
                    array('label' => __('Soporte técnico', 'wally-grow-child'), 'url' => home_url('/soporte-tecnico/')),
                ),
            ),
        ),
        'contact' => array(
            'title' => __('Contacto', 'wally-grow-child'),
            'items' => array(
                __('Buenos Aires, Argentina', 'wally-grow-child'),
                '<a href="mailto:hola@wallygrow.com">hola@wallygrow.com</a>',
                __('Mercado Pago / Transferencia', 'wally-grow-child'),
            ),
        ),
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
                                'alt' => $site_name,
                                'loading' => 'lazy',
                            )
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

            <div class="wg-global-footer__column">
                <h2><?php echo esc_html($content['contact']['title']); ?></h2>
                <ul>
                    <?php foreach ($content['contact']['items'] as $item) : ?>
                        <li><?php echo wp_kses_post($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="wg-global-footer__bottom">
            <p>
                &copy; <?php echo esc_html(wp_date('Y')); ?>
                <?php echo esc_html($site_name); ?>.
                <?php esc_html_e('Botanical Excellence.', 'wally-grow-child'); ?>
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
