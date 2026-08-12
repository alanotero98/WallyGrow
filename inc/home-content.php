<?php
/**
 * Gutenberg source for the bundled Home and its insertable block pattern.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Query args for the Home “Recomendados” product grid.
 *
 * @return array
 */
function wally_grow_get_recommended_products_args() {
    return apply_filters(
        'wally_grow_recommended_products_args',
        array(
            'status'   => 'publish',
            'limit'    => 4,
            'featured' => true,
            'orderby'  => 'date',
            'order'    => 'DESC',
            'return'   => 'objects',
        )
    );
}

/**
 * Featured products for the Home recommendations block.
 *
 * @return WC_Product[]
 */
function wally_grow_get_recommended_products() {
    if (!function_exists('wc_get_products')) {
        return array();
    }

    $products = wc_get_products(wally_grow_get_recommended_products_args());
    return is_array($products) ? $products : array();
}

/**
 * Markup for the recommendations section, or empty string when there are no products.
 *
 * @param string $shop_url Shop permalink.
 * @return string
 */
function wally_grow_get_recommended_products_section($shop_url) {
    $products = wally_grow_get_recommended_products();
    if (!$products) {
        return '';
    }

    $ids = array_map(
        static function ($product) {
            return (int) $product->get_id();
        },
        $products
    );
    $ids = array_filter($ids);

    if (!$ids) {
        return '';
    }

    $ids_attr = implode(',', $ids);
    $shop_url = esc_url($shop_url);

    return <<<BLOCKS
<!-- wp:group {"align":"full","className":"wg-section wg-products","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull wg-section wg-products" id="productos"><!-- wp:group {"align":"wide","className":"wg-products__heading","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide wg-products__heading"><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Recomendados por Wally Grow</h2><!-- /wp:heading --><!-- wp:paragraph --><p>Productos seleccionados para mejorar cada etapa de tu cultivo.</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:paragraph {"className":"wg-text-link"} --><p class="wg-text-link"><a href="{$shop_url}">Ver todos</a></p><!-- /wp:paragraph --></div>
<!-- /wp:group --><!-- wp:shortcode -->[products ids="{$ids_attr}" columns="4" orderby="post__in"]<!-- /wp:shortcode --></div>
<!-- /wp:group -->
BLOCKS;
}

function wally_grow_get_home_blocks() {
    $images = trailingslashit(get_stylesheet_directory_uri()) . 'assets/images/home/';
    $shop_url = function_exists('wc_get_page_permalink')
        ? wc_get_page_permalink('shop')
        : home_url('/');

    $cat_lighting = wally_grow_product_cat_url(array('iluminacion', 'iluminacion-led', 'lighting'), $shop_url);
    $cat_nutrients = wally_grow_product_cat_url(array('nutrientes', 'fertilizantes', 'fertilizantes-organicos'), $shop_url);
    $cat_tents = wally_grow_product_cat_url(array('carpas', 'cultivo-indoor', 'indoor'), $shop_url);
    $cat_accessories = wally_grow_product_cat_url(array('accesorios', 'medicion', 'herramientas'), $shop_url);

    $advice_message = 'Hola Wally Grow, necesito asesoramiento para mi cultivo.';
    $whatsapp_url = wally_grow_get_whatsapp_url();
    $advice_whatsapp_url = wally_grow_get_whatsapp_url($advice_message);
    $has_whatsapp = wally_grow_has_whatsapp();
    $advice_badge = $has_whatsapp
        ? __('ASESORAMIENTO POR WHATSAPP', 'wally-grow-child')
        : __('GUÍA PARA ELEGIR', 'wally-grow-child');
    $advice_lead = $has_whatsapp
        ? __('Contanos qué espacio tenés, en qué etapa estás y qué querés mejorar. En Wally Grow te ayudamos a elegir iluminación, nutrientes, ventilación y accesorios según las necesidades de tu cultivo y tu presupuesto.', 'wally-grow-child')
        : __('Explorá iluminación, nutrientes, ventilación y accesorios según la etapa de tu cultivo y el espacio disponible.', 'wally-grow-child');
    $cta_description = __('Encontrá productos seleccionados para cada etapa de tu cultivo.', 'wally-grow-child');

    $hero_whatsapp_button = $has_whatsapp
        ? '<!-- wp:button {"className":"is-style-outline"} -->'
            . '<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="'
            . esc_url($whatsapp_url)
            . '">Recibir asesoramiento</a></div><!-- /wp:button -->'
        : '';

    $advice_whatsapp_buttons = $has_whatsapp
        ? '<!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"className":"wg-whatsapp-button"} -->'
            . '<div class="wp-block-button wg-whatsapp-button"><a class="wp-block-button__link wp-element-button" href="'
            . esc_url($advice_whatsapp_url)
            . '" target="_blank" rel="noopener noreferrer">Hablar con un asesor por WhatsApp</a></div><!-- /wp:button -->'
            . '</div><!-- /wp:buttons -->'
        : '';

    $cta_whatsapp_button = $has_whatsapp
        ? '<!-- wp:button {"className":"is-style-outline wg-cta__secondary"} -->'
            . '<div class="wp-block-button is-style-outline wg-cta__secondary"><a class="wp-block-button__link wp-element-button" href="'
            . esc_url($advice_whatsapp_url)
            . '" target="_blank" rel="noopener noreferrer">Consultar por WhatsApp</a></div><!-- /wp:button -->'
        : '';

    $reviews_shortcode = trim((string) WALLY_GROW_REVIEWS_SHORTCODE);
    $google_review_url = esc_url_raw((string) WALLY_GROW_GOOGLE_REVIEW_URL);
    $google_reviews_url = esc_url_raw((string) WALLY_GROW_GOOGLE_REVIEWS_URL);
    $reviews_content = '';

    if (
        $reviews_shortcode
        && shortcode_exists('grwi_widget')
        && has_shortcode($reviews_shortcode, 'grwi_widget')
    ) {
        $reviews_content = do_shortcode($reviews_shortcode);
    } elseif (current_user_can('edit_pages')) {
        $reviews_content = '<p class="wg-google-reviews__notice">'
            . esc_html__('Configurá WALLY_GROW_REVIEWS_SHORTCODE con el shortcode real de GR Widget.', 'wally-grow-child')
            . '</p>';
    }

    $reviews_buttons = '';
    if ($google_review_url || $google_reviews_url) {
        $reviews_buttons = '<div class="wg-google-reviews__actions">';
        if ($google_review_url) {
            $reviews_buttons .= '<a class="wg-google-reviews__button wg-google-reviews__button--primary" href="' . esc_url($google_review_url) . '" target="_blank" rel="noopener noreferrer">Escribir una opinión</a>';
        }
        if ($google_reviews_url) {
            $reviews_buttons .= '<a class="wg-google-reviews__button wg-google-reviews__button--secondary" href="' . esc_url($google_reviews_url) . '" target="_blank" rel="noopener noreferrer">Ver todas las opiniones en Google</a>';
        }
        $reviews_buttons .= '</div>';
    }

    $reviews_header = '<div class="wg-google-reviews__header"><p class="wg-google-reviews__eyebrow">Excelente valoración en Google</p><h2>Opiniones reales de clientes de Wally Grow</h2></div>';

    $reviews_block = ($reviews_content || $reviews_buttons)
        ? '<!-- wp:group {"align":"full","className":"wg-section wg-testimonials","layout":{"type":"constrained"}} -->'
            . '<div class="wp-block-group alignfull wg-section wg-testimonials"><!-- wp:html --><div class="wg-google-reviews">'
            . $reviews_header . $reviews_content . $reviews_buttons
            . '</div><!-- /wp:html --></div><!-- /wp:group -->'
        : '';

    $products_section = wally_grow_get_recommended_products_section($shop_url);

    $replace = array(
        '{{hero}}' => esc_url($images . 'wg-home-hero-grow-kit.webp'),
        '{{lighting}}' => esc_url($images . 'wg-category-lighting.webp'),
        '{{nutrients}}' => esc_url($images . 'wg-category-nutrients.webp'),
        '{{tents}}' => esc_url($images . 'wg-category-indoor.webp'),
        '{{accessories}}' => esc_url($images . 'wg-section-tools-store.webp'),
        '{{expertise}}' => esc_url($images . 'wg-section-advice-leaf.webp'),
        '{{shop}}' => esc_url($shop_url),
        '{{cat_lighting}}' => esc_url($cat_lighting),
        '{{cat_nutrients}}' => esc_url($cat_nutrients),
        '{{cat_tents}}' => esc_url($cat_tents),
        '{{cat_accessories}}' => esc_url($cat_accessories),
        '{{hero_whatsapp_button}}' => $hero_whatsapp_button,
        '{{advice_whatsapp_buttons}}' => $advice_whatsapp_buttons,
        '{{cta_whatsapp_button}}' => $cta_whatsapp_button,
        '{{advice_badge}}' => esc_html($advice_badge),
        '{{advice_lead}}' => esc_html($advice_lead),
        '{{cta_description}}' => esc_html($cta_description),
        '{{home}}' => esc_url(home_url('/')),
        '{{products_section}}' => $products_section,
        '{{reviews}}' => $reviews_block,
    );

    $blocks = <<<'BLOCKS'
<!-- wp:group {"align":"full","className":"wg-section wg-hero","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull wg-section wg-hero"><!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"wg-hero__grid"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center wg-hero__grid"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"wg-eyebrow"} -->
<p class="wg-eyebrow">TODO PARA CULTIVAR MEJOR</p>
<!-- /wp:paragraph --><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Hacé crecer tu cultivo con los productos correctos</h1>
<!-- /wp:heading --><!-- wp:paragraph {"className":"wg-lead"} -->
<p class="wg-lead">Encontrá iluminación, fertilizantes, carpas y accesorios seleccionados para cada etapa de tu cultivo.</p>
<!-- /wp:paragraph --><!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button" href="{{shop}}">Ver productos</a></div>
<!-- /wp:button -->{{hero_whatsapp_button}}</div>
<!-- /wp:buttons --><!-- wp:paragraph {"className":"wg-hero__trust"} -->
<p class="wg-hero__trust">Catálogo especializado · Atención personalizada</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"wg-hero__image"} -->
<figure class="wp-block-image size-full wg-hero__image"><img src="{{hero}}" alt="Kit de cultivo indoor con carpa, iluminación LED y nutrientes" width="1536" height="1024" decoding="async" fetchpriority="high"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"wg-section wg-categories","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull wg-section wg-categories" id="categorias"><!-- wp:group {"align":"wide","className":"wg-section-heading","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide wg-section-heading"><!-- wp:heading {"textAlign":"center","level":2} -->
<h2 class="wp-block-heading has-text-align-center">Explorá por categoría</h2>
<!-- /wp:heading --><!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">Encontrá lo que necesitás para cada etapa de tu cultivo.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --><!-- wp:group {"align":"wide","className":"wg-category-grid","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide wg-category-grid"><!-- wp:cover {"url":"{{lighting}}","dimRatio":50,"className":"wg-category wg-category--wide"} -->
<div class="wp-block-cover wg-category wg-category--wide wg-category--lighting"><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{{lighting}}" data-object-fit="cover" width="1200" height="800" loading="lazy" decoding="async"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Iluminación</h3><!-- /wp:heading --><!-- wp:paragraph {"className":"wg-category__cta"} --><p class="wg-category__cta"><a class="wg-card-link" href="{{cat_lighting}}">Ver iluminación</a></p><!-- /wp:paragraph --></div></div>
<!-- /wp:cover --><!-- wp:cover {"url":"{{nutrients}}","dimRatio":50,"className":"wg-category wg-category--nutrients"} -->
<div class="wp-block-cover wg-category wg-category--nutrients"><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{{nutrients}}" data-object-fit="cover" width="1200" height="800" loading="lazy" decoding="async"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Nutrientes</h3><!-- /wp:heading --><!-- wp:paragraph {"className":"wg-category__cta"} --><p class="wg-category__cta"><a class="wg-card-link" href="{{cat_nutrients}}">Ver nutrientes</a></p><!-- /wp:paragraph --></div></div>
<!-- /wp:cover --><!-- wp:cover {"url":"{{tents}}","dimRatio":50,"className":"wg-category wg-category--indoor"} -->
<div class="wp-block-cover wg-category wg-category--indoor"><span aria-hidden="true" class="wp-block-cover__background has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{{tents}}" data-object-fit="cover" width="1200" height="800" loading="lazy" decoding="async"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Cultivo indoor</h3><!-- /wp:heading --><!-- wp:paragraph {"className":"wg-category__cta"} --><p class="wg-category__cta"><a class="wg-card-link" href="{{cat_tents}}">Ver cultivo indoor</a></p><!-- /wp:paragraph --></div></div>
<!-- /wp:cover --><!-- wp:group {"className":"wg-accessories-card","layout":{"type":"default"}} -->
<div class="wp-block-group wg-accessories-card"><!-- wp:group {"layout":{"type":"constrained"}} --><div class="wp-block-group"><!-- wp:paragraph {"className":"wg-accessories-card__eyebrow"} --><p class="wg-accessories-card__eyebrow">CONTROL Y HERRAMIENTAS</p><!-- /wp:paragraph --><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Todo para cuidar cada detalle de tu cultivo</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Instrumentos de medición, ventilación y herramientas para controlar mejor el ambiente y trabajar con mayor precisión.</p><!-- /wp:paragraph --><!-- wp:list {"className":"wg-accessories-card__benefits"} --><ul class="wg-accessories-card__benefits"><li>Medición de pH y temperatura</li><li>Control de humedad y ventilación</li><li>Herramientas para mantenimiento</li></ul><!-- /wp:list --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{{cat_accessories}}">Ver accesorios</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group --><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} --><figure class="wp-block-image size-full"><img src="{{accessories}}" alt="Instrumentos para medir y controlar las condiciones del cultivo" width="1440" height="1080" loading="lazy" decoding="async"/></figure><!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"wg-section wg-expertise","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull wg-section wg-expertise"><!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:cover {"url":"{{expertise}}","dimRatio":0,"className":"wg-expertise__image"} -->
<div class="wp-block-cover wg-expertise__image"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><img class="wp-block-cover__image-background" alt="" src="{{expertise}}" data-object-fit="cover" width="1440" height="1080" loading="lazy" decoding="async"/><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"className":"wg-advice-badge"} --><p class="wg-advice-badge">{{advice_badge}}</p><!-- /wp:paragraph --></div></div>
<!-- /wp:cover --></div>
<!-- /wp:column --><!-- wp:column {"verticalAlignment":"center","className":"wg-expertise__copy"} -->
<div class="wp-block-column is-vertically-aligned-center wg-expertise__copy"><!-- wp:paragraph {"className":"wg-expertise__eyebrow"} -->
<p class="wg-expertise__eyebrow">ASESORAMIENTO PERSONALIZADO</p>
<!-- /wp:paragraph --><!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Tu cultivo no necesita cualquier producto.<br>Necesita una buena recomendación.</h2>
<!-- /wp:heading --><!-- wp:paragraph {"className":"wg-lead"} -->
<p class="wg-lead">{{advice_lead}}</p>
<!-- /wp:paragraph --><!-- wp:list {"className":"wg-check-list"} -->
<ul class="wg-check-list"><li>Recomendaciones según tu cultivo</li><li>Alternativas para distintos presupuestos</li><li>Atención antes y después de la compra</li></ul>
<!-- /wp:list -->{{advice_whatsapp_buttons}}</div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->

{{products_section}}

<!-- wp:group {"align":"full","className":"wg-cta","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull wg-cta" id="marcas"><!-- wp:heading {"textAlign":"center","level":2} --><h2 class="wp-block-heading has-text-align-center">¿Listo para mejorar tu cultivo?</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">{{cta_description}}</p><!-- /wp:paragraph --><!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} --><div class="wp-block-buttons"><!-- wp:button {"className":"wg-cta__primary"} --><div class="wp-block-button wg-cta__primary"><a class="wp-block-button__link wp-element-button" href="{{shop}}">Ir a la tienda</a></div><!-- /wp:button -->{{cta_whatsapp_button}}</div><!-- /wp:buttons --></div>
<!-- /wp:group -->

{{reviews}}

BLOCKS;

    return strtr($blocks, $replace);
}
