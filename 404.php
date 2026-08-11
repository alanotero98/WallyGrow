<?php
/**
 * Friendly recovery page for missing routes.
 */

if (!defined('ABSPATH')) {
    exit;
}

$shop_url = function_exists('wc_get_page_permalink')
    ? wc_get_page_permalink('shop')
    : home_url('/');

get_header();
?>
<section class="wg-system-page" aria-labelledby="wg-not-found-title">
    <div class="wg-system-page__content">
        <p class="wg-eyebrow"><?php esc_html_e('Error 404', 'wally-grow-child'); ?></p>
        <h1 id="wg-not-found-title"><?php esc_html_e('No encontramos esa página', 'wally-grow-child'); ?></h1>
        <p>
            <?php esc_html_e(
                'Puede que el enlace haya cambiado o que la página ya no esté disponible.',
                'wally-grow-child'
            ); ?>
        </p>
        <div class="wg-system-page__actions">
            <a class="button" href="<?php echo esc_url($shop_url); ?>">
                <?php esc_html_e('Ir a la tienda', 'wally-grow-child'); ?>
            </a>
            <a class="button wg-button--secondary" href="<?php echo esc_url(home_url('/')); ?>">
                <?php esc_html_e('Volver al inicio', 'wally-grow-child'); ?>
            </a>
        </div>
    </div>
</section>
<?php
get_footer();
