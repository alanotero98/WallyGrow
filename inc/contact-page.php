<?php
/**
 * Safe fallback content for the public Contact page.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Provide useful contact content without publishing unconfirmed channels.
 *
 * @param string $content Saved page content.
 * @return string
 */
function wally_grow_render_contact_page_content($content) {
    if (!is_page('contacto') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (trim(wp_strip_all_tags(strip_shortcodes($content))) !== '') {
        return $content;
    }

    $email = wally_grow_get_contact_email();
    $whatsapp_url = wally_grow_get_whatsapp_url(
        __('Hola Wally Grow, necesito ayuda con una consulta.', 'wally-grow-child')
    );
    $shop_url = function_exists('wc_get_page_permalink')
        ? wc_get_page_permalink('shop')
        : home_url('/');
    $has_contact_channel = $email !== '' || $whatsapp_url !== '';

    ob_start();
    ?>
    <section class="wg-contact-page" aria-labelledby="wg-contact-title">
        <div class="wg-contact-page__intro">
            <p class="wg-eyebrow"><?php esc_html_e('Estamos para ayudarte', 'wally-grow-child'); ?></p>
            <h2 id="wg-contact-title"><?php esc_html_e('¿En qué te podemos ayudar?', 'wally-grow-child'); ?></h2>
            <p>
                <?php if ($has_contact_channel) : ?>
                    <?php esc_html_e(
                        'Elegí uno de los canales disponibles o recorré la tienda para conocer el catálogo.',
                        'wally-grow-child'
                    ); ?>
                <?php else : ?>
                    <?php esc_html_e(
                        'Mientras confirmamos los canales de atención, podés recorrer la tienda y conocer el catálogo.',
                        'wally-grow-child'
                    ); ?>
                <?php endif; ?>
            </p>
        </div>

        <?php if ($has_contact_channel) : ?>
            <div class="wg-contact-page__methods">
                <?php if ($email !== '') : ?>
                    <a class="wg-contact-page__method" href="mailto:<?php echo esc_attr($email); ?>">
                        <strong><?php esc_html_e('Correo electrónico', 'wally-grow-child'); ?></strong>
                        <span><?php echo esc_html($email); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($whatsapp_url !== '') : ?>
                    <a
                        class="wg-contact-page__method"
                        href="<?php echo esc_url($whatsapp_url); ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <strong><?php esc_html_e('WhatsApp', 'wally-grow-child'); ?></strong>
                        <span><?php esc_html_e('Iniciar una conversación', 'wally-grow-child'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="wg-contact-page__notice" role="status">
                <h3><?php esc_html_e('Canales de atención', 'wally-grow-child'); ?></h3>
                <p>
                    <?php esc_html_e(
                        'Los canales de contacto se van a publicar cuando estén confirmados.',
                        'wally-grow-child'
                    ); ?>
                </p>
            </div>
        <?php endif; ?>

        <a class="button wg-contact-page__shop" href="<?php echo esc_url($shop_url); ?>">
            <?php esc_html_e('Ir a la tienda', 'wally-grow-child'); ?>
        </a>
    </section>
    <?php

    return ob_get_clean();
}
add_filter('the_content', 'wally_grow_render_contact_page_content', 20);
